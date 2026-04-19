<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SaleController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $query = $project->sales()->with(['client', 'units'])->orderByDesc('sale_date');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhere('id', 'like', "%{$search}%");
        }

        $sales = $query->paginate(10)->appends($request->query());

        $statuses = ['reserved', 'sold', 'cancelled'];

        return view('sales.index', compact('project', 'sales', 'statuses'));
    }

    public function create(Project $project)
    {
        $clients = $project->clients()->orderBy('name')->get();
        $units = $project->units()->where('status', 'available')->get();
        return view('sales.create', compact('project', 'clients', 'units'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'units' => 'required|array|min:1',
            'units.*' => 'exists:units,id',
            'status' => ['required', Rule::in(['reserved', 'sold'])],
            'payment_method' => 'nullable|string|max:255',
            'sale_date' => 'nullable|date',
            'paid_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::transaction(function () use ($request, $project) {
                $units = Unit::whereIn('id', $request->units)->get();
                $totalAmount = $units->sum('sale_price');
                $discount = $request->discount ?? 0;
                $paidAmount = $request->paid_amount ?? 0;

                // Calculate net total after discount
                $netTotal = $totalAmount - $discount;

                // Validation checks
                if ($discount > $totalAmount) {
                    throw new \Exception('Discount cannot exceed total amount.');
                }

                if ($paidAmount > $netTotal) {
                    throw new \Exception('Paid amount cannot exceed net total (Total - Discount).');
                }

                $sale = Sale::create([
                    'project_id' => $project->id,
                    'client_id' => $request->client_id,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'discount' => $discount,
                    'status' => $request->status,
                    'payment_method' => $request->payment_method,
                    'sale_date' => $request->sale_date,
                ]);

                foreach ($units as $unit) {
                    $sale->units()->attach($unit->id, ['unit_price' => $unit->sale_price]);
                    $unit->update(['status' => $request->status]);
                }
            });

            return redirect()->route('sales.index', $project->slug)
                ->with('success', 'Sale created successfully.');
        } catch (\Exception $e) {
            // CRITICAL: Use withInput() to preserve form data
            return redirect()->back()
                ->withInput()  // This preserves all form inputs
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Project $project, Sale $sale)
    {
        $clients = Client::orderBy('name')->get();
        $units = $project->units()->whereIn('status', ['available', 'reserved'])->orWhereIn('id', $sale->units->pluck('id'))->get();

        return view('sales.create', compact('project', 'sale', 'clients', 'units'));
    }

    public function update(Request $request, Project $project, Sale $sale)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'units' => 'required|array|min:1',
            'units.*' => 'exists:units,id',
            'status' => ['required', Rule::in(['reserved', 'sold'])],
            'payment_method' => 'nullable|string|max:255',
            'sale_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($request, $sale) {
            // Reset old units to available
            $sale->units()->each(fn($unit) => $unit->update(['status' => 'available']));
            $sale->units()->detach();

            $units = Unit::whereIn('id', $request->units)->get();
            $totalAmount = $units->sum('sale_price');
            $discount = $request->discount ?? 0;
            $paidAmount = $request->paid_amount ?? 0;

            // Calculate net total after discount
            $netTotal = $totalAmount - $discount;

            // Validation checks
            if ($discount > $totalAmount) {
                throw new \Exception('Discount cannot exceed total amount.');
            }

            if ($paidAmount > $netTotal) {
                throw new \Exception('Paid amount cannot exceed net total (Total - Discount).');
            }
            $sale->update([
                'client_id' => $request->client_id,
                'total_amount' => $totalAmount,
                'paid_amount' => $request->paid_amount ?? 0,
                'status' => $request->status,
                'payment_method' => $request->payment_method,
                'sale_date' => $request->sale_date,
                'discount' => $discount,
            ]);

            foreach ($units as $unit) {
                $sale->units()->attach($unit->id, ['unit_price' => $unit->sale_price]);
                $unit->update(['status' => $request->status]);
            }
        });

        return redirect()->route('sales.index', $project->slug)
            ->with('success', 'Sale updated successfully.');
    }

    public function show(Project $project, Sale $sale)
    {
        $sale->load('client', 'units', 'payments');
        return view('sales.show', compact('project', 'sale'));
    }

    public function destroy(Project $project, Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Reset units to available
            $sale->units()->each(fn($unit) => $unit->update(['status' => 'available']));
            $sale->units()->detach();
            $sale->delete();
        });

        return redirect()->route('sales.index', $project->slug)
            ->with('success', 'Sale deleted successfully.');
    }
}
