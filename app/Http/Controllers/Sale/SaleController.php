<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Payment;
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
            'client_id'      => 'required|exists:clients,id',
            'units'          => 'required|array|min:1',
            'units.*'        => 'exists:units,id',
            'status'         => ['required', Rule::in(['reserved', 'sold'])],
            'payment_method' => 'nullable|string|max:255',
            'sale_date'      => 'nullable|date',
            'discount'       => 'nullable|numeric|min:0',

            // Optional initial payment fields
            'initial_amount'          => 'nullable|numeric|min:0',
            'initial_payment_date'    => 'nullable|date',
            'initial_method'          => 'nullable|in:cash,cheque,bank_transfer,online',
            'initial_bank_name'       => 'nullable|string|max:255',
            'initial_cheque_no'       => 'nullable|string|max:100',
            'initial_account_no'      => 'nullable|string|max:100',
            'initial_transaction_ref' => 'nullable|string|max:255',
            'initial_notes'           => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $project) {
                $units       = Unit::whereIn('id', $request->units)->get();
                $totalAmount = $units->sum('sale_price');
                $discount    = $request->discount ?? 0;
                $netTotal    = $totalAmount - $discount;

                if ($discount > $totalAmount) {
                    throw new \Exception('Discount cannot exceed total amount.');
                }

                // Validate initial payment does not exceed net total
                $initialAmount = $request->initial_amount ?? 0;
                if ($initialAmount > $netTotal) {
                    throw new \Exception('Initial payment cannot exceed net total (Total − Discount).');
                }

                // Create the sale (no paid_amount column — it is computed from payments)
                $sale = Sale::create([
                    'project_id'     => $project->id,
                    'client_id'      => $request->client_id,
                    'total_amount'   => $totalAmount,
                    'discount'       => $discount,
                    'status'         => $request->status,
                    'payment_method' => $request->payment_method,
                    'sale_date'      => $request->sale_date,
                ]);

                // Attach units
                foreach ($units as $unit) {
                    $sale->units()->attach($unit->id, ['unit_price' => $unit->sale_price]);
                    $unit->update(['status' => $request->status]);
                }

                // Record initial payment if provided
                if ($initialAmount > 0) {
                    $sale->payments()->create([
                        'amount'          => $initialAmount,
                        'payment_date'    => $request->initial_payment_date ?? $request->sale_date ?? now(),
                        'method'          => $request->initial_method ?? 'cash',
                        'bank_name'       => $request->initial_bank_name,
                        'cheque_no'       => $request->initial_cheque_no,
                        'account_no'      => $request->initial_account_no,
                        'transaction_ref' => $request->initial_transaction_ref,
                        'notes'           => $request->initial_notes ?? 'On booking',
                    ]);
                }
            });

            return redirect()->route('sales.index', $project->slug)
                ->with('success', 'Sale created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Project $project, Sale $sale)
    {
        $clients = Client::orderBy('name')->get();
        $units   = $project->units()
            ->whereIn('status', ['available', 'reserved'])
            ->orWhereIn('id', $sale->units->pluck('id'))
            ->get();

        return view('sales.create', compact('project', 'sale', 'clients', 'units'));
    }

    public function update(Request $request, Project $project, Sale $sale)
    {
        $request->validate([
            'client_id'      => 'required|exists:clients,id',
            'units'          => 'required|array|min:1',
            'units.*'        => 'exists:units,id',
            'status'         => ['required', Rule::in(['reserved', 'sold'])],
            'payment_method' => 'nullable|string|max:255',
            'sale_date'      => 'nullable|date',
            'discount'       => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $project, $sale) {
                $units       = Unit::whereIn('id', $request->units)->get();
                $totalAmount = $units->sum('sale_price');
                $discount    = $request->discount ?? 0;
                $netTotal    = $totalAmount - $discount;

                if ($discount > $totalAmount) {
                    throw new \Exception('Discount cannot exceed total amount.');
                }

                // Guard: existing payments must not exceed the new net total
                $alreadyPaid = $sale->payments()->sum('amount');
                if ($alreadyPaid > $netTotal) {
                    throw new \Exception(
                        'Already recorded payments (₨ ' . number_format($alreadyPaid, 2) .
                        ') exceed the new net total (₨ ' . number_format($netTotal, 2) .
                        '). Adjust payments before changing the sale.'
                    );
                }

                // Reset old units to available
                $sale->units()->each(fn($unit) => $unit->update(['status' => 'available']));
                $sale->units()->detach();

                $sale->update([
                    'client_id'      => $request->client_id,
                    'total_amount'   => $totalAmount,
                    'discount'       => $discount,
                    'status'         => $request->status,
                    'payment_method' => $request->payment_method,
                    'sale_date'      => $request->sale_date,
                ]);

                foreach ($units as $unit) {
                    $sale->units()->attach($unit->id, ['unit_price' => $unit->sale_price]);
                    $unit->update(['status' => $request->status]);
                }
            });

            return redirect()->route('sales.index', $project->slug)
                ->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Project $project, Sale $sale)
    {
        $sale->load('client', 'units', 'payments');
        return view('sales.show', compact('project', 'sale'));
    }

    public function destroy(Project $project, Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->units()->each(fn($unit) => $unit->update(['status' => 'available']));
            $sale->units()->detach();
            $sale->payments()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index', $project->slug)
            ->with('success', 'Sale deleted successfully.');
    }

    public function report(Project $project, Sale $sale)
    {
        $sale->load('client', 'units', 'payments');
        return view('sales.report', compact('project', 'sale'));
    }
}