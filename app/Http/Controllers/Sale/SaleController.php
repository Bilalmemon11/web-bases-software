<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\Client;
use App\Models\Project;
use App\Models\Installment;
use App\Models\Payment;
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
            'discount' => 'nullable|numeric|min:0',
            'has_installments' => 'nullable|boolean',
            'installment_count' => 'required_if:has_installments,1|nullable|integer|min:1|max:50',
            'initial_payment' => 'nullable|numeric|min:0',
            'installments' => 'required_if:has_installments,1|nullable|array',
            'installments.*.number' => 'required|integer',
            'installments.*.amount' => 'required|numeric|min:0',
            'installments.*.due_date' => 'required|date',
            'installments.*.description' => 'nullable|string',
            'paid_amount' => 'required_unless:has_installments,1|nullable|numeric|min:0',
            'payment_method' => 'required_unless:has_installments,1|nullable|in:cash,cheque,bank_transfer,online',
            'cheque_no' => 'required_if:payment_method,cheque|nullable|string',
            'bank' => 'required_if:payment_method,cheque,bank_transfer|nullable|string',
            'initial_payment_method' => 'required_with:initial_payment|nullable|in:cash,cheque,bank_transfer,online',
            'initial_cheque_no' => 'required_if:initial_payment_method,cheque|nullable|string',
            'initial_bank' => 'required_if:initial_payment_method,cheque,bank_transfer|nullable|string',
            'initial_description' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $project) {
                $units = Unit::whereIn('id', $request->units)->get();
                $totalAmount = $units->sum('sale_price');
                $discount = $request->discount ?? 0;
                $hasInstallments = $request->has_installments ?? false;

                // Validation: Discount cannot exceed total
                if ($discount > $totalAmount) {
                    throw new \Exception('Discount cannot exceed total amount.');
                }

                $netTotal = $totalAmount - $discount;

                // Create Sale
                $sale = Sale::create([
                    'project_id' => $project->id,
                    'client_id' => $request->client_id,
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0, // Will be updated by payments
                    'discount' => $discount,
                    'status' => $request->status,
                    'payment_method' => $hasInstallments ? 'installments' : $request->payment_method,
                    'sale_date' => $request->sale_date,
                    'has_installments' => $hasInstallments,
                    'installment_count' => $hasInstallments ? $request->installment_count : null,
                ]);

                // Attach units
                foreach ($units as $unit) {
                    $sale->units()->attach($unit->id, ['unit_price' => $unit->sale_price]);
                    $unit->update(['status' => $request->status]);
                }

                // Handle Installments
                if ($hasInstallments && $request->installments) {
                    $initialPayment = $request->initial_payment ?? 0;
                    
                    foreach ($request->installments as $inst) {
                        Installment::create([
                            'sale_id' => $sale->id,
                            'installment_number' => $inst['number'],
                            'amount_due' => $inst['amount'],
                            'amount_paid' => 0,
                            'due_date' => $inst['due_date'],
                            'description' => $inst['description'] ?? ($inst['number'] == 0 ? 'Initial/Booking Payment' : 'Monthly Installment ' . $inst['number']),
                        ]);
                    }

                    // Create payment record for initial payment if provided
                    if ($initialPayment > 0 && $request->filled('initial_payment_method')) {
                        $initialInstallment = Installment::where('sale_id', $sale->id)
                            ->where('installment_number', 0)
                            ->first();

                        Payment::create([
                            'sale_id' => $sale->id,
                            'installment_id' => $initialInstallment ? $initialInstallment->id : null,
                            'payment_date' => $request->sale_date ?? now(),
                            'amount' => $initialPayment,
                            'payment_method' => $request->initial_payment_method,
                            'cheque_no' => $request->initial_cheque_no,
                            'bank' => $request->initial_bank,
                            'description' => $request->initial_description ?? 'Initial/Booking Payment',
                        ]);
                    }
                } else {
                    // Direct payment - create single payment record
                    $paidAmount = $request->paid_amount ?? 0;
                    
                    if ($paidAmount > $netTotal) {
                        throw new \Exception('Paid amount cannot exceed net total (Total - Discount).');
                    }

                    if ($paidAmount > 0 && $request->filled('payment_method')) {
                        Payment::create([
                            'sale_id' => $sale->id,
                            'payment_date' => $request->sale_date ?? now(),
                            'amount' => $paidAmount,
                            'payment_method' => $request->payment_method,
                            'cheque_no' => $request->cheque_no,
                            'bank' => $request->bank,
                            'description' => $request->payment_description ?? 'Initial payment',
                        ]);
                    }
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
        
        // Load installments with payments to check if any have been paid
        $sale->load(['installments', 'payments']);
        
        $units = $project->units()
            ->whereIn('status', ['available', 'reserved'])
            ->orWhereIn('id', $sale->units->pluck('id'))
            ->get();

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
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $sale) {
            // Reset old units to available
            $sale->units()->each(fn($unit) => $unit->update(['status' => 'available']));
            $sale->units()->detach();

            $units = Unit::whereIn('id', $request->units)->get();
            $totalAmount = $units->sum('sale_price');
            $discount = $request->discount ?? 0;
            $paidAmount = $request->paid_amount ?? $sale->paid_amount;

            $netTotal = $totalAmount - $discount;

            if ($discount > $totalAmount) {
                throw new \Exception('Discount cannot exceed total amount.');
            }

            if (!$sale->has_installments && $paidAmount > $netTotal) {
                throw new \Exception('Paid amount cannot exceed net total (Total - Discount).');
            }

            $sale->update([
                'client_id' => $request->client_id,
                'total_amount' => $totalAmount,
                'paid_amount' => $sale->has_installments ? $sale->paid_amount : $paidAmount,
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
        $sale->load('client', 'units', 'payments', 'installments');
        return view('sales.show', compact('project', 'sale'));
    }

    public function destroy(Project $project, Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->units()->each(fn($unit) => $unit->update(['status' => 'available']));
            $sale->units()->detach();
            $sale->delete();
        });

        return redirect()->route('sales.index', $project->slug)
            ->with('success', 'Sale deleted successfully.');
    }
}