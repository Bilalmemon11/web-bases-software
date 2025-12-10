<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\PredefinedCategory;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $project->load(['expenses']);

        $query = $project->expenses()->orderBy('expense_date', 'desc');

        // 🔹 Apply filters dynamically
        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        if ($request->filled('date')) {
            $query->whereDate('expense_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(10)->appends($request->query());

        $category = PredefinedCategory::all();

        return view('expenses.index', compact('project', 'expenses', 'category'));
    }

    public function store(Request $request, Project $project)
    {
        $request->merge([
            'expense_type' => $request->expense_type ?: null,
            'new_expense_type' => $request->new_expense_type ?: null,
        ]);
        try {
            $addclass = '';
            $removeclass = '';
            $validated = $request->validate([
                'date' => 'required|date',
                'expense_type' => 'nullable|required_without:new_expense_type|string|min:5|max:255',
                'new_expense_type' => 'nullable|required_without:expense_type|string|min:5|max:255|unique:predefined_categories,name',
                'description' => 'nullable|string|max:1000',
                'amount' => 'required|numeric|min:1',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $category = $validated['expense_type'] ?? $validated['new_expense_type'];

            $expenseData = [
                'project_id' => $project->id,
                'category' => $category,
                'description' => $validated['description'] ?? null,
                'amount' => $validated['amount'],
                'expense_date' => $validated['date'],
            ];
            
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('expenses_attachments', 'public');
                $expenseData['attachment'] = $path;
            }

            $project->expenses()->create($expenseData);
            return redirect()->route('expenses.index', $project->slug)
                ->with('success', 'Expense added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->filled('new_expense_type')) {
                $addclass = 'new_expense_type@d-block,expense_type@d-none';
                $removeclass = 'expense_type@d-block,new_expense_type@d-none';
            } else {
                $addclass = 'new_expense_type@d-none,expense_type@d-block';
                $removeclass = 'expense_type@d-none,new_expense_type@d-block';
            }
            return redirect()
                ->route('expenses.index', ['project' => $project->slug, 'addclass' => $addclass, 'removeclass' => $removeclass])
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->filled('new_expense_type')) {
                $addclass = 'new_expense_type@d-block,expense_type@d-none';
                $removeclass = 'expense_type@d-block,new_expense_type@d-none';
            } else {
                $addclass = 'new_expense_type@d-none,expense_type@d-block';
                $removeclass = 'expense_type@d-none,new_expense_type@d-block';
            }
            return redirect()
                ->route('expenses.index', ['project' => $project->slug, 'addclass' => $addclass, 'removeclass' => $removeclass])
                ->with('error', 'An error occurred while updating the member.')
                ->withInput();
        }
    }

    public function update(Request $request, Project $project, Expense $expense)
    {
        $request->merge([
            'expense_type' => $request->expense_type ?: null,
            'new_expense_type' => $request->new_expense_type ?: null,
        ]);

        try {
            $addclass = '';
            $removeclass = '';

            $validated = $request->validate([
                'date' => 'required|date',
                'expense_type' => 'nullable|required_without:new_expense_type|string|min:5|max:255',
                'new_expense_type' => [
                    'nullable',
                    'required_without:expense_type',
                    'string',
                    'min:5',
                    'max:255',
                    Rule::unique('predefined_categories', 'name')->ignore($expense->category, 'name'),
                ],
                'description' => 'nullable|string|max:1000',
                'amount' => 'required|numeric|min:1',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            // Determine category (existing or new)
            $category = $validated['expense_type'] ?? $validated['new_expense_type'];
            
            // Prepare data for update
            $expenseData = [
                'category' => $category,
                'description' => $validated['description'] ?? null,
                'amount' => $validated['amount'],
                'expense_date' => $validated['date'],
            ];

            // Handle attachment upload/replacement
            if ($request->hasFile('attachment')) {
                // Delete old file if it exists
                if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
                    Storage::disk('public')->delete($expense->attachment);
                }

                $path = $request->file('attachment')->store('expenses_attachments', 'public');
                $expenseData['attachment'] = $path;
            }

            // Update expense record
            $expense->update($expenseData);

            return redirect()
                ->route('expenses.index', $project->slug)
                ->with('success', 'Expense updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Maintain toggle UI state based on user input
            if ($request->filled('new_expense_type')) {
                $addclass = 'new_expense_type_' . $expense->id . '@d-block,expense_type_' . $expense->id . '@d-none';
                $removeclass = 'expense_type_' . $expense->id . '@d-block,new_expense_type_' . $expense->id . '@d-none';
            } else {
                $addclass = 'new_expense_type_' . $expense->id . '@d-none,expense_type_' . $expense->id . '@d-block';
                $removeclass = 'expense_type_' . $expense->id . '@d-none,new_expense_type_' . $expense->id . '@d-block';
            }

            return redirect()
                ->route('expenses.index', [
                    'project' => $project->slug,
                    'addclass' => $addclass,
                    'removeclass' => $removeclass,
                    'openpopup' => 'editExpenseModal-' . $expense->id
                ])
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Handle other unexpected errors gracefully
            if ($request->filled('new_expense_type')) {
                $addclass = 'new_expense_type_' . $expense->id . '@d-block,expense_type_' . $expense->id . '@d-none';
                $removeclass = 'expense_type_' . $expense->id . '@d-block,new_expense_type_' . $expense->id . '@d-none';
            } else {
                $addclass = 'new_expense_type_' . $expense->id . '@d-none,expense_type_' . $expense->id . '@d-block';
                $removeclass = 'expense_type_' . $expense->id . '@d-none,new_expense_type_' . $expense->id . '@d-block';
            }

            return redirect()
                ->route('expenses.index', [
                    'project' => $project->slug,
                    'addclass' => $addclass,
                    'removeclass' => $removeclass,
                    'openpopup' => 'editExpenseModal-' . $expense->id
                ])
                ->with('error', 'An error occurred while updating the expense.')
                ->withInput();
        }
    }


    public function destroy(Project $project, Expense $expense)
    {
        if ($expense->project_id !== $project->id) {
            return redirect()
                ->route('expenses.index', $project->slug)
                ->with('error', 'Expense not found in this project.');
        }

        try {
            $expense->delete();
            return redirect()
                ->route('expenses.index', $project->slug)
                ->with('success', 'Expense deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('expenses.index', $project->slug)
                ->with('error', 'An error occurred while deleting the expense.');
        }
    }
}
