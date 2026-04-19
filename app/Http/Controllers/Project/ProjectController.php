<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\PredefinedUnit;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        // Fetch projects with related members and units
        $projects = Project::with(['members', 'units'])->latest()->get();

        return view('projects.index', compact('projects'));
    }
    public function select(Project $project, Request $request)
    {
        // Save selected project info in session
        session([
            'active_project_id' => $project->id,
            'active_project_name' => $project->name,
            'active_project_start_date' => $project->start_date,
            'active_project_status' => $project->status,
            'active_project_slug' => $project->slug,
        ]);

        // Redirect to project dashboard
        return redirect()->route('projects.dashboard', $project->slug);
    }
    public function create()
    {
        $members = Member::where('is_manager', false)->orderBy('name')->get();
        $manager = Member::where('is_manager', true)->first();
        $predefinedUnits = PredefinedUnit::orderBy('type')->get();
        return view('projects.create', compact('members', 'predefinedUnits', 'manager'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_investment' => 'required|numeric|min:10',
            'land_cost' => 'nullable|numeric|min:0',
            // 'sale_price' => 'nullable|numeric|min:0',

            // Manager validation (separate from other members)
            'manager_investment_amount' => 'required|numeric|min:1',
            'manager_profit_share' => 'required|numeric|min:0|max:100',

            // Existing members validation
            'existing_members.*.investment_amount' => 'required|numeric|min:1',
            'existing_members.*.profit_share' => 'required|numeric|min:0|max:100',

            // New members validation
            'new_members.*.name' => 'required_with:new_members.*|string|max:255',
            'new_members.*.phone' => 'nullable|string|regex:/^[0-9]{11}$/|unique:members,phone',
            'new_members.*.cnic' => 'nullable|string|regex:/^[0-9]{13}$/|unique:members,cnic',
            'new_members.*.address' => 'nullable|string',
            'new_members.*.investment_amount' => 'required|numeric|min:1',
            'new_members.*.profit_share' => 'required|numeric|min:0|max:100',

            // Units validation
            'units.*.type' => 'nullable|string|max:100',
            'units.*.size' => 'nullable|string|max:50',
            'units.*.sale_price' => 'nullable|numeric|min:0',
            'units.*.qty' => 'nullable|numeric|min:1',
        ]);

        // ==================== CUSTOM BUSINESS LOGIC VALIDATION ====================

        $manager = Member::where('is_manager', true)->first();

        if (!$manager) {
            return $this->ajaxResponse($request, [
                'manager' => ['Manager not found in the system. Please contact administrator.']
            ]);
        }

        $totalInvestment = $validated['total_investment'];
        $landCost = $validated['land_cost'] ?? 0;

        // Manager investment
        $managerInvestment = floatval($validated['manager_investment_amount']);
        $managerProfitShare = floatval($validated['manager_profit_share']);

        // 1. Validate Land Cost doesn't exceed Total Investment
        if ($landCost > 0 && $landCost > $totalInvestment) {
            return $this->ajaxResponse($request, [
                'land_cost' => ['Land cost (₨' . number_format($landCost, 2) . ') cannot exceed total investment (₨' . number_format($totalInvestment, 2) . ').']
            ]);
        }

        // 2. Calculate total member investments (including manager)
        $totalMemberInvestment = $managerInvestment; // Start with manager investment
        $memberInvestments = [];

        // Add manager to investments array
        $memberInvestments[] = [
            'type' => 'manager',
            'id' => $manager->id,
            'name' => $manager->name,
            'investment' => $managerInvestment,
            'share' => $managerProfitShare
        ];

        // Collect existing member investments
        if ($request->has('existing_members')) {
            foreach ($request->existing_members as $memberId => $data) {
                $investment = floatval($data['investment_amount']);
                $totalMemberInvestment += $investment;

                $memberInvestments[] = [
                    'type' => 'existing',
                    'id' => $memberId,
                    'investment' => $investment,
                    'share' => floatval($data['profit_share'])
                ];
            }
        }

        // Collect new member investments
        if ($request->has('new_members')) {
            foreach ($request->new_members as $index => $data) {
                if (!empty($data['name'])) {
                    $investment = floatval($data['investment_amount']);
                    $totalMemberInvestment += $investment;

                    $memberInvestments[] = [
                        'type' => 'new',
                        'name' => $data['name'],
                        'investment' => $investment,
                        'share' => floatval($data['profit_share'])
                    ];
                }
            }
        }

        // 3. Validate total member investment doesn't exceed project investment
        if ($totalMemberInvestment > $totalInvestment) {
            return $this->ajaxResponse($request, [
                'total_investment' => [sprintf(
                    'Total member investments (₨%s) exceed the project total investment (₨%s). Please adjust member investments or increase the total investment amount.',
                    number_format($totalMemberInvestment, 2),
                    number_format($totalInvestment, 2)
                )]
            ]);
        }

        // 4. Validate individual member investment doesn't exceed total investment
        foreach ($memberInvestments as $mi) {
            if ($mi['investment'] > $totalInvestment) {
                $memberIdentifier = $mi['type'] === 'new'
                    ? $mi['name']
                    : ($mi['type'] === 'manager' ? 'Manager (' . $mi['name'] . ')' : 'Member ID: ' . $mi['id']);

                return $this->ajaxResponse($request, [
                    'total_investment' => [sprintf(
                        '%s has an investment amount (₨%s) that exceeds the total project investment (₨%s).',
                        $memberIdentifier,
                        number_format($mi['investment'], 2),
                        number_format($totalInvestment, 2)
                    )]
                ]);
            }
        }

        // 5. Validate profit shares sum to 100% or less
        $totalProfitShare = 0;
        foreach ($memberInvestments as $mi) {
            $totalProfitShare += $mi['share'];
        }

        if ($totalProfitShare > 100) {
            return $this->ajaxResponse($request, [
                'profit_share' => [sprintf(
                    'Total profit share (%.2f%%) exceeds 100%%. Please adjust the profit share percentages. You need to reduce by %.2f%%.',
                    $totalProfitShare,
                    $totalProfitShare - 100
                )]
            ]);
        }

        // 6. Warning if profit shares don't add up to 100%
        if ($totalProfitShare < 99.99 && $totalProfitShare > 0) {
            // This will be shown as a warning toast after success
            session()->flash('warning', sprintf(
                'Note: Total profit share is %.2f%%. The remaining %.2f%% is unallocated.',
                $totalProfitShare,
                100 - $totalProfitShare
            ));
        }

        // 7. Validate profit share roughly matches investment proportion (info only)
        $proportionMismatches = [];
        foreach ($memberInvestments as $mi) {
            $expectedShare = ($mi['investment'] / $totalInvestment) * 100;
            $actualShare = $mi['share'];
            $tolerance = 5; // 5% tolerance

            if (abs($expectedShare - $actualShare) > $tolerance) {
                $memberIdentifier = $mi['type'] === 'new'
                    ? $mi['name']
                    : ($mi['type'] === 'manager' ? $mi['name'] . ' (Manager)' : 'Member ID: ' . $mi['id']);

                $proportionMismatches[] = sprintf(
                    '%s: Share %.2f%% vs Expected %.2f%%',
                    $memberIdentifier,
                    $actualShare,
                    $expectedShare
                );
            }
        }

        if (!empty($proportionMismatches)) {
            session()->flash('info', 'Profit share differs from investment proportion: ' . implode('; ', $proportionMismatches));
        }

        // 8. Validate units total cost doesn't exceed investment (optional warning)
        if ($request->has('units')) {
            // $totalUnitCost = 0;
            // foreach ($request->units as $unitData) {
            //     $totalUnitCost += floatval($unitData['cost_price'] ?? 0);
            // }

            // if ($totalUnitCost > $totalInvestment) {
            //     session()->flash('warning', sprintf(
            //         'Warning: Total unit costs (₨%s) exceed project investment (₨%s).',
            //         number_format($totalUnitCost, 2),
            //         number_format($totalInvestment, 2)
            //     ));
            // }
        }

        // ==================== CREATE PROJECT ====================

        DB::transaction(function () use ($request, $validated, $manager) {
            // Create project
            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'total_investment' => $validated['total_investment'],
                'land_cost' => $validated['land_cost'] ?? 0,
                // 'sale_price' => $validated['sale_price'] ?? null,
            ]);

            // Attach Manager first
            $project->members()->attach($manager->id, [
                'investment_amount' => $validated['manager_investment_amount'],
                'profit_share' => $validated['manager_profit_share'],
                'role' => 'manager',
            ]);

            // Attach existing members with pivot data
            if ($request->has('existing_members')) {
                foreach ($request->existing_members as $memberId => $data) {
                    $project->members()->attach($memberId, [
                        'investment_amount' => $data['investment_amount'],
                        'profit_share' => $data['profit_share'],
                        'role' => 'investor',
                    ]);
                }
            }

            // Create and attach new members
            if ($request->has('new_members')) {
                foreach ($request->new_members as $data) {
                    if (!empty($data['name'])) {
                        $member = Member::create([
                            'name' => $data['name'],
                            'phone' => $data['phone'] ?? null,
                            'cnic' => $data['cnic'] ?? null,
                            'address' => $data['address'] ?? null,
                        ]);

                        $project->members()->attach($member->id, [
                            'investment_amount' => $data['investment_amount'],
                            'profit_share' => $data['profit_share'],
                            'role' => 'investor',
                        ]);
                    }
                }
            }

            // Create units
            if ($request->has('units')) {
                
                    foreach ($request->units as $unitData) {
                        $qty = isset($unitData['qty']) && is_numeric($unitData['qty']) && $unitData['qty'] > 0 ? intval($unitData['qty']) : 1;
                        for ($i = 0; $i < $qty; $i++) {
                            $project->units()->create([
                                // 'unit_no' => $unitData['unit_no'] ?? 'UNIT-' . strtoupper(Str::random(6)),
                                'type' => $unitData['type'] ?? null,
                                'size' => $unitData['size'] ?? null,
                                'sale_price' => $unitData['sale_price'] ?? null,
                                'status' => 'available',
                            ]);
                        }
                    }
            }
        });

        // Return JSON response for AJAX, redirect for normal requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!',
                'redirect' => route('projects.index')
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Helper method to handle AJAX error responses
     */
    private function ajaxResponse($request, $errors)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'errors' => $errors
            ], 422);
        }

        return back()->withErrors($errors)->withInput();
    }

    public function dashboard(Project $project)
    {
        // Ensure user is viewing the active project
        if (session('active_project_id') !== $project->id) {
            return redirect()->route('projects.select', $project->slug);
        }

        $project->load(['members', 'units', 'expenses', 'sales']);

        $stats = [
            'total_investment' => $project->members->sum('pivot.investment_amount'),

            'land_cost' => $project->land_cost ?? 0,

            'total_expenses' => $project->total_expenses,

            'total_sales' => DB::table('sale_units')
                ->join('sales', 'sales.id', '=', 'sale_units.sale_id')
                ->where('sales.project_id', $project->id)
                ->sum('sale_units.unit_price'),

            'total_received' => $project->sales()->sum('paid_amount'),

            'total_pending' => $project->sales()->sum(DB::raw('total_amount - paid_amount - discount')),

            'current_balance' => $project->members->sum('pivot.investment_amount')
                - $project->total_expenses
                + $project->sales()->sum('paid_amount'),

            'total_members' => $project->members->count(),
            'total_units' => $project->units()->count(),
            'units_sold' => $project->units()->where('status', 'sold')->count(),
            'units_reserved' => $project->units()->where('status', 'reserved')->count(),
            'progress' => $project->progress,
            'discounts_given' => $project->total_discounts,
            'net_sales' => $project->total_sales - $project->total_discounts,
        ];

        return view('projects.dashboard', compact('project', 'stats'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'land_cost' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,archived,on_hold',
        ]);

        // Update slug if name changed
        if ($project->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
            // session(['active_project_slug' => $validated['slug']]);
        }

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'land_cost' => $validated['land_cost'] ?? 0,
            'sale_price' => $validated['sale_price'] ?? null,
            'status' => $validated['status'],
            'slug' => $validated['slug'] ?? $project->slug,
        ]);

        return redirect()->back()->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        DB::transaction(function () use ($project) {
            // Delete project-specific sales (payments and sale_units will cascade)
            $project->sales()->each(function ($sale) {
                $sale->delete();
            });

            // Delete units associated with the project
            $project->units()->each(function ($unit) {
                $unit->delete();
            });

            // Delete expenses associated with the project
            $project->expenses()->each(function ($expense) {
                $expense->delete();
            });

            // Delete generated reports for the project
            $project->reports()->each(function ($report) {
                $report->delete();
            });

            // Detach members from the project (pivot will cascade but explicit detach is safe)
            $project->members()->detach();

            // Clear active project session if this project is active
            if (session('active_project_id') === $project->id) {
                session()->forget([
                    'active_project_id',
                    'active_project_name',
                    'active_project_start_date',
                    'active_project_status',
                    'active_project_slug',
                ]);
            }

            // Finally, delete the project
            $project->delete();
        });

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
