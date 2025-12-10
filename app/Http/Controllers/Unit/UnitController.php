<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PredefinedUnit;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $project->load(['units']);

        $query = $project->units()->orderBy('unit_no');

        // Apply filters dynamically
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unit_no', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('sale_price', 'like', "%{$search}%");
            });
        }

        $units = $query->paginate(10)->appends($request->query());

        // For dropdown filters
        $predefinedUnits = PredefinedUnit::pluck('type')->unique();
        $statuses = Unit::select('status')->distinct()->pluck('status');
        return view('units.index', compact('project', 'units', 'predefinedUnits', 'statuses'));
    }

    public function store(Request $request, Project $project)
    {
        $request->merge([
            'unit_type' => $request->unit_type ?: null,
            'new_unit_type' => $request->new_unit_type ?: null,
        ]);

        try {
            $validated = $request->validate([
                'unit_type' => 'nullable|required_without:new_unit_type|string|min:3|max:255',
                'new_unit_type' => 'nullable|required_without:unit_type|string|min:3|max:255|unique:predefined_units,type',
                'size' => 'nullable|string|max:255',
                'sale_price' => 'nullable|numeric|min:0',
                'qty' => 'required|numeric|min:1',
            ]);

            $type = $validated['unit_type'] ?? $validated['new_unit_type'];

            // Get the last unit for this project
            $lastUnit = Unit::where('project_id', $project->id)->orderByDesc('id')->first();
            $letter = 'A';
            $number = 1;

            if ($lastUnit && preg_match('/Unit-([A-Z])-?(\d+)/i', $lastUnit->unit_no, $matches)) {
                $letter = $matches[1];
                $number = intval($matches[2]) + 1;
            }

            // Prepare bulk insert data
            $unitsData = [];
            for ($i = 0; $i < $validated['qty']; $i++) {
                if ($number > 99) {
                    $letter = chr(ord($letter) + 1);
                    $number = 1;
                }

                $unitNo = sprintf('Unit-%s%02d', $letter, $number++);

                $unitsData[] = [
                    'project_id' => $project->id,
                    'type' => $type,
                    'size' => $validated['size'] ?? null,
                    'sale_price' => $validated['sale_price'] ?? 0,
                    'unit_no' => $unitNo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Unit::insert($unitsData);

            return redirect()->back()
                ->with('success', 'Unit(s) added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while saving the unit.')->withInput();
        }
    }


    public function update(Request $request, Project $project, Unit $unit)
    {
        $request->merge([
            'unit_type' => $request->unit_type ?: null,
            'new_unit_type' => $request->new_unit_type ?: null,
        ]);

        try {
            $validated = $request->validate([
                'unit_type' => 'nullable|required_without:new_unit_type|string|min:3|max:255',
                'new_unit_type' => [
                    'nullable',
                    'required_without:unit_type',
                    'string',
                    'min:3',
                    'max:255',
                    Rule::unique('predefined_units', 'type')->ignore($unit->type, 'type'),
                ],
                'size' => 'nullable|string|max:255',
                'sale_price' => 'nullable|numeric|min:0',
            ]);

            $type = $validated['unit_type'] ?? $validated['new_unit_type'];

            $updateData = [
                'type' => $type,
                'size' => $validated['size'] ?? null,
                'sale_price' => $validated['sale_price'] ?? 0,
            ];

            $unit->update($updateData);

            return redirect()->back()
                ->with('success', 'Unit updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while updating the unit.')->withInput();
        }
    }

    public function destroy(Project $project, Unit $unit)
    {
        // Ensure the unit belongs to the current project
        if ($unit->project_id !== $project->id) {
            return redirect()->back()->with('error', 'Unit not found in this project.');
        }

        if ($unit->sales()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete unit associated with sales. Please remove associated sales first.');
        }
        try {
            $unit->delete();
            return redirect()->back()->with('success', 'Unit deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the unit.');
        }
    }
}
