{{-- resources/views/payments/_form.blade.php --}}
{{-- Shared partial used by both create & edit payment views --}}

<div class="row g-3">

    {{-- Amount --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₨</span>
            <input type="number" step="0.01" min="0.01" name="amount"
                   class="form-control @error('amount') is-invalid @enderror"
                   value="{{ old('amount', $payment->amount ?? '') }}"
                   placeholder="0.00" required oninput="currencyFormat(this, 'amount-display')">
        </div>
        <small id="amount-display" class="d-block text-muted"></small>

        @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
        <small class="text-muted">Outstanding: ₨ {{ number_format($sale->remaining_amount + ($payment->amount ?? 0), 2) }}</small>
    </div>

    {{-- Payment Date --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
        <input type="date" name="payment_date"
               class="form-control @error('payment_date') is-invalid @enderror"
               value="{{ old('payment_date', isset($payment) ? $payment->payment_date?->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('payment_date') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Method --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
        <select name="method" id="paymentMethod" class="form-select @error('method') is-invalid @enderror"
                onchange="toggleMethodFields()" required>
            <option value="">— Select —</option>
            <option value="cash"          {{ old('method', $payment->method ?? '') == 'cash'          ? 'selected' : '' }}>Cash</option>
            <option value="cheque"        {{ old('method', $payment->method ?? '') == 'cheque'        ? 'selected' : '' }}>Cheque</option>
            <option value="bank_transfer" {{ old('method', $payment->method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="online"        {{ old('method', $payment->method ?? '') == 'online'        ? 'selected' : '' }}>Online Transfer</option>
        </select>
        @error('method') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Bank Name (cheque / bank_transfer) --}}
    <div class="col-sm-6 method-field field-cheque field-bank_transfer" style="display:none;">
        <label class="form-label fw-semibold">Bank Name</label>
        <input type="text" name="bank_name" class="form-control"
               value="{{ old('bank_name', $payment->bank_name ?? '') }}" placeholder="e.g. MCB, HBL">
        @error('bank_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Cheque No (cheque only) --}}
    <div class="col-sm-6 method-field field-cheque" style="display:none;">
        <label class="form-label fw-semibold">Cheque No</label>
        <input type="text" name="cheque_no" class="form-control"
               value="{{ old('cheque_no', $payment->cheque_no ?? '') }}" placeholder="e.g. 0012345">
        @error('cheque_no') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Account No (bank_transfer only) --}}
    <div class="col-sm-6 method-field field-bank_transfer" style="display:none;">
        <label class="form-label fw-semibold">Account No</label>
        <input type="text" name="account_no" class="form-control"
               value="{{ old('account_no', $payment->account_no ?? '') }}" placeholder="e.g. PK36 MUCB 0001 0010 0023 0010">
        @error('account_no') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Transaction Ref (bank_transfer / online) --}}
    <div class="col-sm-6 method-field field-bank_transfer field-online" style="display:none;">
        <label class="form-label fw-semibold">Transaction Reference</label>
        <input type="text" name="transaction_ref" class="form-control"
               value="{{ old('transaction_ref', $payment->transaction_ref ?? '') }}" placeholder="TXN / UTR no.">
        @error('transaction_ref') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- Notes --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Notes <span class="text-muted">(optional)</span></label>
        <textarea name="notes" rows="2" class="form-control"
                  placeholder="Any remarks about this payment…">{{ old('notes', $payment->notes ?? '') }}</textarea>
    </div>

</div>

@push('scripts')
<script>
function toggleMethodFields() {
    const method = document.getElementById('paymentMethod').value;
    document.querySelectorAll('.method-field').forEach(el => el.style.display = 'none');
    if (method) {
        document.querySelectorAll(`.field-${method}`).forEach(el => el.style.display = '');
    }
}
// Run on page load to restore old values after validation failure
document.addEventListener('DOMContentLoaded', toggleMethodFields);
</script>
@endpush