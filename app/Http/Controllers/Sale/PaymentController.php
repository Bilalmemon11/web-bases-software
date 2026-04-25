<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Sale;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Show the "Add Payment" form for a given sale.
     */
    public function create(Project $project, Sale $sale)
    {
        $sale->load('client', 'units', 'payments');
        return view('payments.create', compact('project', 'sale'));
    }

    /**
     * Store a new payment.
     */
    public function store(Request $request, Project $project, Sale $sale)
    {
        $data = $request->validate([
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'method'          => 'required|in:cash,cheque,bank_transfer,online',
            'bank_name'       => 'nullable|string|max:255',
            'cheque_no'       => 'nullable|string|max:100',
            'account_no'      => 'nullable|string|max:100',
            'transaction_ref' => 'nullable|string|max:255',
            'notes'           => 'nullable|string|max:500',
        ]);

        // Guard: don't allow overpayment
        $remaining = $sale->remaining_amount;
        if ($data['amount'] > $remaining) {
            return back()->withInput()
                ->with('error', "Payment amount (₨ " . number_format($data['amount'], 2) . ") exceeds outstanding balance (₨ " . number_format($remaining, 2) . ").");
        }

        $sale->payments()->create($data);

        // Auto-mark as sold if fully paid and currently reserved
        if ($sale->remaining_amount <= 0 && $sale->status === 'reserved') {
            $sale->update(['status' => 'sold']);
            $sale->units()->update(['status' => 'sold']);
        }

        return redirect()->route('sales.show', [$project->slug, $sale->id])
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show edit form for a payment.
     */
    public function edit(Project $project, Sale $sale, Payment $payment)
    {
        $sale->load('client', 'units', 'payments');
        return view('payments.edit', compact('project', 'sale', 'payment'));
    }

    /**
     * Update a payment record.
     */
    public function update(Request $request, Project $project, Sale $sale, Payment $payment)
    {
        $data = $request->validate([
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'method'          => 'required|in:cash,cheque,bank_transfer,online',
            'bank_name'       => 'nullable|string|max:255',
            'cheque_no'       => 'nullable|string|max:100',
            'account_no'      => 'nullable|string|max:100',
            'transaction_ref' => 'nullable|string|max:255',
            'notes'           => 'nullable|string|max:500',
        ]);

        // Guard: new total must not exceed net_amount
        $otherPayments = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');
        if (($otherPayments + $data['amount']) > $sale->net_amount) {
            return back()->withInput()
                ->with('error', 'Total payments would exceed the net sale amount.');
        }

        $payment->update($data);

        return redirect()->route('sales.show', [$project->slug, $sale->id])
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Delete a payment record.
     */
    public function destroy(Project $project, Sale $sale, Payment $payment)
    {
        $payment->delete();

        return redirect()->route('sales.show', [$project->slug, $sale->id])
            ->with('success', 'Payment deleted.');
    }
    public function report(Project $project, Sale $sale)
    {
        $sale->load('client', 'units', 'payments');

        // If you later add an Installment model/relationship, eager-load it here:
        // $sale->load('client', 'units', 'payments', 'installments');

        return view('sales.report', compact('project', 'sale'));
    }
}
