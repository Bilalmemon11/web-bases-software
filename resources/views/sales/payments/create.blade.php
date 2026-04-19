@extends('layouts.app')
@section('title', 'Record Payment')

@section('content')
<section>
    <div class="d-flex justify-content-between mb-3">
        <h4>Record Payment - Sale #{{ $sale->id }}</h4>
        <a href="{{ route('sales.show', [$project->slug, $sale->id]) }}" class="btn btn-secondary">Back to Sale</a>
    </div>

    <div class="row">
        {{-- Payment Form --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave text-success me-2"></i>Payment Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.payments.store', [$project->slug, $sale->id]) }}" method="POST">
                        @csrf

                        {{-- Installment Selection (if applicable) --}}
                        @if($sale->has_installments)
                        <div class="mb-3">
                            <label class="form-label">Select Installment (optional)</label>
                            <select name="installment_id" id="installmentSelect" class="form-select" onchange="updatePaymentInfo()">
                                <option value="">-- General Payment (not linked to specific installment) --</option>
                                @foreach($sale->installments as $inst)
                                @if($inst->remaining_amount != 0)
                                <option value="{{ $inst->id }}" 
                                    data-due="{{ $inst->amount_due }}"
                                    data-paid="{{ $inst->amount_paid }}"
                                    data-remaining="{{ $inst->remaining_amount }}"
                                    data-duedate="{{ $inst->due_date->format('Y-m-d') }}"
                                    {{ old('installment_id') == $inst->id ? 'selected' : '' }}>
                                    Installment #{{ $inst->installment_number }} - 
                                    Due: ₨{{ number_format($inst->amount_due, 2) }} | 
                                    Paid: ₨{{ number_format($inst->amount_paid, 2) }} | 
                                    Remaining: ₨{{ number_format($inst->remaining_amount, 2) }}
                                    @if($inst->status === 'paid')
                                        <span class="text-success">[PAID]</span>
                                    @endif
                                </option>
                                @endif
                                @endforeach
                            </select>
                            <small class="text-muted">Leave blank for general payment not tied to a specific installment</small>
                        </div>

                        <div id="installmentInfo" class="alert alert-info d-none mb-3">
                            <strong>Selected Installment:</strong><br>
                            Due Date: <span id="infoDueDate"></span><br>
                            Amount Due: ₨<span id="infoDue"></span><br>
                            Amount Paid: ₨<span id="infoPaid"></span><br>
                            Remaining: ₨<span id="infoRemaining"></span>
                        </div>
                        @endif

                        {{-- Payment Date --}}
                        <div class="mb-3">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" 
                                value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" step="0.01" name="amount" id="paymentAmount" 
                                    class="form-control" value="{{ old('amount') }}" 
                                    placeholder="e.g., 75000" required
                                    oninput="currencyFormat(this, 'amountDisplay')">
                            </div>
                            <small id="amountDisplay" class="d-block text-muted"></small>
                            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Payment Method --}}
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="paymentMethod" class="form-select" required onchange="togglePaymentFields()">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                            @error('payment_method') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Cheque/Bank Details --}}
                        <div id="chequeFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Cheque No. <span class="text-danger">*</span></label>
                                <input type="text" name="cheque_no" class="form-control" 
                                    value="{{ old('cheque_no') }}" placeholder="e.g., 170682604">
                                @error('cheque_no') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div id="bankFields" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Bank <span class="text-danger">*</span></label>
                                <input type="text" name="bank" class="form-control" 
                                    value="{{ old('bank') }}" placeholder="e.g., MCB, HBL, UBL">
                                @error('bank') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label">Description (optional)</label>
                            <textarea name="description" class="form-control" rows="2" 
                                placeholder="e.g., On Booking of Plot # Pearl 014">{{ old('description') }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('sales.show', [$project->slug, $sale->id]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sale Summary --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Sale Summary</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Client:</strong> {{ $sale->client->name }}</p>
                    <p class="mb-2"><strong>Units:</strong> {{ $sale->units->pluck('unit_no')->join(', ') }}</p>
                    <hr>
                    <p class="mb-2"><strong>Total Amount:</strong> ₨{{ number_format($sale->total_amount, 2) }}</p>
                    <p class="mb-2"><strong>Discount:</strong> ₨{{ number_format($sale->discount, 2) }}</p>
                    <p class="mb-2"><strong>Net Total:</strong> ₨{{ number_format($sale->net_amount, 2) }}</p>
                    <p class="mb-2"><strong>Total Paid:</strong> <span class="text-success">₨{{ number_format($sale->paid_amount, 2) }}</span></p>
                    <p class="mb-0"><strong>Remaining:</strong> <span class="text-danger">₨{{ number_format($sale->pending_amount, 2) }}</span></p>
                    
                    @if($sale->has_installments)
                    <hr>
                    <p class="mb-1"><small class="text-muted">Installment Plan: {{ $sale->installment_count }} installments</small></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function togglePaymentFields() {
        const method = document.getElementById('paymentMethod').value;
        document.getElementById('chequeFields').style.display = method === 'cheque' ? 'block' : 'none';
        document.getElementById('bankFields').style.display = (method === 'cheque' || method === 'bank_transfer') ? 'block' : 'none';
    }

    function updatePaymentInfo() {
        const select = document.getElementById('installmentSelect');
        const option = select.options[select.selectedIndex];
        const info = document.getElementById('installmentInfo');
        
        if (option.value) {
            document.getElementById('infoDueDate').textContent = option.dataset.duedate;
            document.getElementById('infoDue').textContent = parseFloat(option.dataset.due).toLocaleString('en-PK', {minimumFractionDigits: 2});
            document.getElementById('infoPaid').textContent = parseFloat(option.dataset.paid).toLocaleString('en-PK', {minimumFractionDigits: 2});
            document.getElementById('infoRemaining').textContent = parseFloat(option.dataset.remaining).toLocaleString('en-PK', {minimumFractionDigits: 2});
            info.classList.remove('d-none');
            
            // Auto-fill amount with remaining
            document.getElementById('paymentAmount').value = option.dataset.remaining;
            currencyFormat(document.getElementById('paymentAmount'), 'amountDisplay');
        } else {
            info.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        togglePaymentFields();
        updatePaymentInfo();
    });
</script>
@endpush
@endsection