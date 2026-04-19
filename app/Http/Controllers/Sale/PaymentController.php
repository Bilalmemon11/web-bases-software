<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Show payment form for a sale
     */
    public function create(Project $project, Sale $sale)
    {
        $sale->load('installments', 'payments');
        return view('sales.payments.create', compact('project', 'sale'));
    }

    /**
     * Store a new payment
     */
    public function store(Request $request, Project $project, Sale $sale)
    {
        $request->validate([
            'installment_id' => 'nullable|exists:installments,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer',
            'cheque_no' => 'required_if:payment_method,cheque|nullable|string',
            'bank' => 'required_if:payment_method,cheque,bank_transfer|nullable|string',
            'description' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $sale) {
                // Validate amount doesn't exceed remaining
                $netTotal = $sale->net_amount;
                $totalPaid = $sale->paid_amount;
                $remaining = $netTotal - $totalPaid;

                if ($request->amount > $remaining) {
                    throw new \Exception("Payment amount (₨" . number_format($request->amount, 2) . ") exceeds remaining balance (₨" . number_format($remaining, 2) . ")");
                }

                // If payment is for specific installment, validate
                if ($request->installment_id) {
                    $installment = Installment::findOrFail($request->installment_id);
                    $installmentRemaining = $installment->remaining_amount;
                    
                    if ($request->amount > $installmentRemaining) {
                        throw new \Exception("Payment amount exceeds installment remaining (₨" . number_format($installmentRemaining, 2) . ")");
                    }
                }

                // Create payment
                Payment::create([
                    'sale_id' => $sale->id,
                    'installment_id' => $request->installment_id,
                    'payment_date' => $request->payment_date,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'cheque_no' => $request->cheque_no,
                    'bank' => $request->bank,
                    'description' => $request->description,
                ]);

                // The Payment model's boot method will automatically sync paid_amount
            });

            return redirect()->route('sales.show', [$project->slug, $sale->id])
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show edit form for a payment
     */
    public function edit(Project $project, Sale $sale, Payment $payment)
    {
        $sale->load('installments');
        return view('sales.payments.edit', compact('project', 'sale', 'payment'));
    }

    /**
     * Update a payment
     */
    public function update(Request $request, Project $project, Sale $sale, Payment $payment)
    {
        $request->validate([
            'installment_id' => 'nullable|exists:installments,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer',
            'cheque_no' => 'required_if:payment_method,cheque|nullable|string',
            'bank' => 'required_if:payment_method,cheque,bank_transfer|nullable|string',
            'description' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $sale, $payment) {
                // Calculate available amount (excluding this payment)
                $netTotal = $sale->net_amount;
                $totalPaid = $sale->paid_amount - $payment->amount; // Exclude current payment
                $remaining = $netTotal - $totalPaid;

                if ($request->amount > $remaining) {
                    throw new \Exception("Payment amount exceeds remaining balance");
                }

                $payment->update([
                    'installment_id' => $request->installment_id,
                    'payment_date' => $request->payment_date,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'cheque_no' => $request->cheque_no,
                    'bank' => $request->bank,
                    'description' => $request->description,
                ]);
            });

            return redirect()->route('sales.show', [$project->slug, $sale->id])
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a payment
     */
    public function destroy(Project $project, Sale $sale, Payment $payment)
    {
        try {
            $payment->delete();
            
            return redirect()->route('sales.show', [$project->slug, $sale->id])
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }
}