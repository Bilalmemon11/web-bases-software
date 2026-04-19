@extends('layouts.app')
@section('title', isset($sale) ? 'Edit Sale' : 'Create Sale')

@section('content')
<section>
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ isset($sale) ? 'Edit Sale #' . $sale->id : 'Create New Sale' }}</h4>
        <a href="{{ route('sales.index', $project->slug) }}" class="btn btn-secondary">Back to Sales</a>
    </div>
    <form
        action="{{ isset($sale) ? route('sales.update', [$project->slug, $sale->id]) : route('sales.store', $project->slug) }}"
        method="POST"
        class="needs-validation h-100" novalidate>
        @csrf
        @if(isset($sale)) @method('PUT') @endif

        <div class="row g-4 h-100">
            {{-- LEFT SIDE: UNITS --}}
            <div class="col-lg-8 h-100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light justify-content-between d-flex align-items-center">
                        <h5 class="mb-0"><i class="fas fa-building text-primary me-2"></i>Select Units</h5>
                        <input
                            type="text"
                            id="unitSearch"
                            class="form-control form-control-sm" style="max-width: 200px;"
                            placeholder="Search units..."
                            onkeyup="filterUnits()">
                    </div>
                    <div class="card-body h-100 overflow-y-auto">
                        <div class="row g-3">
                            @foreach($units as $unit)
                            @php
                            $isSelected = isset($sale) && in_array($unit->id, $sale->units->pluck('id')->toArray());
                            $isSelected = $isSelected || (old('units') && in_array($unit->id, old('units')));
                            $disabled = $unit->status === 'sold' && !$isSelected;
                            @endphp
                            <div class="col-lg-4 col-sm-6">
                                <div
                                    class="card unit-card {{ $isSelected ? 'border-primary bg-warning-subtle' : '' }} {{ $disabled ? 'opacity-50' : '' }}"
                                    style="cursor: pointer;"
                                    onclick="toggleUnit({{ $unit->id }}, {{ $unit->sale_price ? $unit->sale_price : 0 }}, '{{ $unit->unit_no }}', '{{ $unit->type }}', '{{ $unit->size }}', {{ $disabled ? 'true':'false' }})"
                                    id="unit-card-{{ $unit->id }}">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $unit->unit_no }}</strong>
                                            <span class="badge p-1
                                            {{ $unit->status == 'available' ? 'bg-success' : ($unit->status == 'reserved' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ ucfirst($unit->status) }}
                                            </span>
                                        </div>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-home me-1"></i> {{ ucfirst($unit->type) }}
                                        </p>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-ruler-combined me-1"></i> {{ $unit->size ?? '—' }}
                                        </p>
                                        <p class="fw-semibold text-end text-primary mb-0 d-flex justify-content-between align-items-center">
                                            <span class="badge bg-secondary p-1">{{ format_currency_unit($unit->sale_price, 2) }}</span>
                                            <span class="small">₨ {{ number_format($unit->sale_price, 2) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: SALE SUMMARY --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-receipt text-success me-2"></i>Sale Summary</h5>
                    </div>
                    <div class="card-body">

                        {{-- Client --}}
                        <div class="mb-3">
                            <label class="form-label">Client</label>
                            <select name="client_id" class="form-select select2" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $sale->client_id ?? '') == $client->id ? 'selected':'' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="saleStatus" class="form-select" required>
                                <option value="reserved" {{ old('status', $sale->status ?? '')=='reserved'?'selected':'' }}>Reserved</option>
                                <option value="sold" {{ old('status', $sale->status ?? '')=='sold'?'selected':'' }}>Sold</option>
                            </select>
                        </div>

                        {{-- Sale Date --}}
                        <div class="mb-3">
                            <label class="form-label">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control"
                                value="{{ old('sale_date', isset($sale) ? $sale->sale_date?->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>

                        {{-- Discount --}}
                        <div class="mb-3">
                            <label class="form-label">Discount (optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" step="0.01" name="discount" id="discount"
                                    class="form-control" value="{{ old('discount', $sale->discount ?? 0) }}"
                                    placeholder="e.g., 50000"
                                    oninput="currencyFormat(this, 'discount-display'); updateSummary()">
                            </div>
                            <small id="discount-display" class="d-block text-muted"></small>
                        </div>

                        {{-- INSTALLMENT TOGGLE --}}
                        @if(!isset($sale) || !$sale->has_installments || $sale->payments->count() == 0)
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="hasInstallments" 
                                    name="has_installments" value="1"
                                    {{ old('has_installments', $sale->has_installments ?? false) ? 'checked' : '' }}
                                    onchange="toggleInstallmentSection()">
                                <label class="form-check-label" for="hasInstallments">
                                    <strong>Enable Installment Plan</strong>
                                </label>
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="has_installments" value="{{ $sale->has_installments ? 1 : 0 }}">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Payment type cannot be changed after payments are recorded.</small>
                        </div>
                        @endif

                        {{-- INSTALLMENT SECTION --}}
                        <div id="installmentSection" style="display: none;">
                            <div class="border rounded p-3 mb-3 bg-light">
                                <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Installment Setup</h6>
                                
                                @if(!isset($sale) || $sale->installments->count() == 0)
                                <div class="mb-3">
                                    <label class="form-label">Number of Installments</label>
                                    <input type="number" name="installment_count" id="installmentCount" 
                                        class="form-control" min="1" max="50"
                                        value="{{ old('installment_count', $sale->installment_count ?? 12) }}"
                                        oninput="generateInstallmentFields()">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Initial/Booking Payment (optional)</label>
                                    <input type="number" step="0.01" name="initial_payment" id="initialPayment"
                                        class="form-control" value="{{ old('initial_payment', 0) }}"
                                        placeholder="e.g., 600000"
                                        oninput="generateInstallmentFields();currencyFormat(this,'initialPaymentCurDisplay');toggleInitialPaymentFields()">
                                    <small id="initialPaymentCurDisplay" class="d-block text-muted"></small>
                                </div>

                                {{-- Initial Payment Method Fields --}}
                                <div id="initialPaymentMethodSection" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select name="initial_payment_method" id="initialPaymentMethod" class="form-select" onchange="toggleInitialPaymentMethodFields()">
                                            <option value="">Select Method</option>
                                            <option value="cash" {{ old('initial_payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="cheque" {{ old('initial_payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                            <option value="bank_transfer" {{ old('initial_payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="online" {{ old('initial_payment_method') == 'online' ? 'selected' : '' }}>Online Transfer</option>
                                        </select>
                                        @error('initial_payment_method') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div id="initialChequeFields" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Cheque No. <span class="text-danger">*</span></label>
                                            <input type="text" name="initial_cheque_no" class="form-control" 
                                                value="{{ old('initial_cheque_no') }}" placeholder="e.g., 170682604">
                                            @error('initial_cheque_no') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>

                                    <div id="initialBankFields" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Bank <span class="text-danger">*</span></label>
                                            <input type="text" name="initial_bank" class="form-control" 
                                                value="{{ old('initial_bank') }}" placeholder="e.g., MCB, HBL, UBL">
                                            @error('initial_bank') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description (optional)</label>
                                        <textarea name="initial_description" class="form-control" rows="2" 
                                            placeholder="e.g., On Booking of Plot # Pearl 014">{{ old('initial_description') }}</textarea>
                                        @error('initial_description') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Starting Due Date</label>
                                    <input type="date" name="first_due_date" id="firstDueDate"
                                        class="form-control" value="{{ old('first_due_date', date('Y-m-d')) }}"
                                        onchange="generateInstallmentFields()">
                                </div>

                                <button type="button" class="btn btn-sm btn-primary w-100" onclick="generateInstallmentFields()">
                                    <i class="fas fa-sync me-1"></i> Generate Installments
                                </button>

                                <div id="installmentsList" class="mt-3"></div>
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <small>Installments cannot be modified after creation. Use payment records to track payments.</small>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- DIRECT PAYMENT SECTION (shown when installments disabled) --}}
                        <div id="directPaymentSection">
                            <div class="mb-3">
                                <label class="form-label">Paid Amount</label>
                                <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control"
                                    value="{{ old('paid_amount', $sale->paid_amount ?? 0) }}"
                                    oninput="currencyFormat(this,'paidDisplay'); updateSummary(); toggleDirectPaymentFields()">
                                <small id="paidDisplay" class="d-block"></small>
                            </div>

                            {{-- Direct Payment Method Fields --}}
                            <div id="directPaymentMethodSection" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="paymentMethod" class="form-select" onchange="toggleDirectPaymentMethodFields()">
                                        <option value="">Select Method</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Transfer</option>
                                    </select>
                                    @error('payment_method') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div id="directChequeFields" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Cheque No. <span class="text-danger">*</span></label>
                                        <input type="text" name="cheque_no" class="form-control" 
                                            value="{{ old('cheque_no') }}" placeholder="e.g., 170682604">
                                        @error('cheque_no') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div id="directBankFields" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Bank <span class="text-danger">*</span></label>
                                        <input type="text" name="bank" class="form-control" 
                                            value="{{ old('bank') }}" placeholder="e.g., MCB, HBL, UBL">
                                        @error('bank') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description (optional)</label>
                                    <textarea name="payment_description" class="form-control" rows="2" 
                                        placeholder="e.g., Initial payment">{{ old('payment_description') }}</textarea>
                                    @error('payment_description') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="border-top pt-3">
                            <p class="mb-1"><strong>Selected Units:</strong> <span id="selectedUnitsCount">0</span></p>
                            <p class="mb-1"><strong>Total Price:</strong> ₨ <span id="totalPriceDisplay">0.00</span></p>
                            <p class="mb-1"><strong>Discount:</strong> ₨ <span id="discountDisplay">0.00</span></p>
                            <p class="mb-1"><strong>Net Total:</strong> ₨ <span id="netTotalDisplay">0.00</span></p>
                            <p><strong>Amount to Distribute:</strong> ₨ <span id="remainingDisplay">0.00</span></p>
                        </div>

                        {{-- Hidden field for units --}}
                        <input type="hidden" name="units[]" id="selectedUnits">

                        {{-- Submit --}}
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-save me-2"></i>{{ isset($sale) ? 'Update Sale' : 'Create Sale' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
    let selectedUnits = new Map();

    function toggleUnit(id, price, unit_no, type, size, disabled) {
        if (disabled) return;

        const card = document.getElementById(`unit-card-${id}`);
        if (selectedUnits.has(id)) {
            selectedUnits.delete(id);
            card.classList.remove('border-primary', 'bg-warning-subtle');
        } else {
            selectedUnits.set(id, { price, unit_no });
            card.classList.add('border-primary', 'bg-warning-subtle');
        }
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        selectedUnits.forEach(u => total += parseFloat(u.price));

        const count = selectedUnits.size;
        const discount = parseFloat(document.getElementById('discount')?.value || 0);
        const paid = parseFloat(document.getElementById('paidAmount')?.value || 0);

        const netTotal = total - discount;
        const remaining = netTotal - paid;

        document.getElementById('selectedUnitsCount').textContent = count;
        document.getElementById('totalPriceDisplay').textContent = total.toLocaleString('en-PK', { minimumFractionDigits: 2 });
        document.getElementById('discountDisplay').textContent = discount.toLocaleString('en-PK', { minimumFractionDigits: 2 });
        document.getElementById('netTotalDisplay').textContent = netTotal.toLocaleString('en-PK', { minimumFractionDigits: 2 });
        document.getElementById('remainingDisplay').textContent = remaining.toLocaleString('en-PK', { minimumFractionDigits: 2 });

        // Update hidden units inputs
        const form = document.querySelector('form');
        document.querySelectorAll('input[name="units[]"]').forEach(h => h.remove());

        selectedUnits.forEach((_, id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'units[]';
            input.value = id;
            form.appendChild(input);
        });
    }

    function toggleInstallmentSection() {
        const hasInstallments = document.getElementById('hasInstallments').checked;
        document.getElementById('installmentSection').style.display = hasInstallments ? 'block' : 'none';
        document.getElementById('directPaymentSection').style.display = hasInstallments ? 'none' : 'block';
        
        if (hasInstallments) {
            generateInstallmentFields();
        }
    }

    function toggleInitialPaymentFields() {
        const amount = parseFloat(document.getElementById('initialPayment')?.value || 0);
        document.getElementById('initialPaymentMethodSection').style.display = amount > 0 ? 'block' : 'none';
    }

    function toggleInitialPaymentMethodFields() {
        const method = document.getElementById('initialPaymentMethod')?.value;
        document.getElementById('initialChequeFields').style.display = method === 'cheque' ? 'block' : 'none';
        document.getElementById('initialBankFields').style.display = (method === 'cheque' || method === 'bank_transfer') ? 'block' : 'none';
    }

    function toggleDirectPaymentFields() {
        const amount = parseFloat(document.getElementById('paidAmount')?.value || 0);
        document.getElementById('directPaymentMethodSection').style.display = amount > 0 ? 'block' : 'none';
    }

    function toggleDirectPaymentMethodFields() {
        const method = document.getElementById('paymentMethod')?.value;
        document.getElementById('directChequeFields').style.display = method === 'cheque' ? 'block' : 'none';
        document.getElementById('directBankFields').style.display = (method === 'cheque' || method === 'bank_transfer') ? 'block' : 'none';
    }

    function generateInstallmentFields() {
        const count = parseInt(document.getElementById('installmentCount')?.value || 12);
        const initialPayment = parseFloat(document.getElementById('initialPayment')?.value || 0);
        const firstDueDate = document.getElementById('firstDueDate')?.value;
        
        let total = 0;
        selectedUnits.forEach(u => total += parseFloat(u.price));
        const discount = parseFloat(document.getElementById('discount')?.value || 0);
        const netTotal = total - discount;
        
        const amountToDistribute = netTotal - initialPayment;
        const installmentAmount = (amountToDistribute / count).toFixed(2);
        
        let html = '<div class="list-group">';
        
        // Initial payment if exists
        if (initialPayment > 0) {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Initial Payment (Booking)</strong>
                        <span class="badge bg-success">₨ ${parseFloat(initialPayment).toLocaleString('en-PK')}</span>
                    </div>
                    <input type="hidden" name="installments[0][number]" value="0">
                    <input type="hidden" name="installments[0][amount]" value="${initialPayment}">
                    <input type="date" name="installments[0][due_date]" class="form-control form-control-sm" value="${firstDueDate}">
                    <input type="text" value="On booking" name="installments[0][description]" class="form-control form-control-sm mt-1" placeholder="Description (optional)">
                </div>
            `;
        }
        
        // Regular installments
        for (let i = 1; i <= count; i++) {
            const dueDate = calculateDueDate(firstDueDate, i);
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Installment ${i}</strong>
                        <span class="badge bg-primary">₨ ${parseFloat(installmentAmount).toLocaleString('en-PK')}</span>
                    </div>
                    <input type="hidden" name="installments[${i}][number]" value="${i}">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="installments[${i}][amount]" class="form-control form-control-sm" value="${installmentAmount}" placeholder="Amount">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="installments[${i}][due_date]" class="form-control form-control-sm" value="${dueDate}">
                        </div>
                        <div class="col-12">
                            <input type="text" value="Installment no ${i}" name="installments[${i}][description]" class="form-control form-control-sm" placeholder="Description (optional)">
                        </div>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        document.getElementById('installmentsList').innerHTML = html;
    }

    function calculateDueDate(startDate, monthsToAdd) {
        const date = new Date(startDate);
        date.setMonth(date.getMonth() + monthsToAdd);
        return date.toISOString().split('T')[0];
    }

    function initializeSelectedUnits() {
        const preselectedCards = document.querySelectorAll('.unit-card.border-primary');
        preselectedCards.forEach(card => {
            const unitId = card.id.replace('unit-card-', '');
            const priceText = card.querySelector('.text-primary .small').textContent;
            const price = parseFloat(priceText.replace(/[^\d.-]/g, ''));
            const unitNo = card.querySelector('strong').textContent;
            selectedUnits.set(parseInt(unitId), { price: price, unit_no: unitNo });
        });
        updateSummary();
    }

    function filterUnits() {
        let input = document.getElementById("unitSearch").value.toLowerCase();
        let unitCards = document.querySelectorAll(".unit-card");
        unitCards.forEach(card => {
            let text = card.innerText.toLowerCase();
            card.parentElement.style.display = text.includes(input) ? "" : "none";
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('.select2').select2({ width: '100%' });
        initializeSelectedUnits();
        toggleInstallmentSection();
        toggleInitialPaymentFields();
        toggleInitialPaymentMethodFields();
        toggleDirectPaymentFields();
        toggleDirectPaymentMethodFields();
        
        // If editing and has installments, show them
        @if(isset($sale) && $sale->has_installments)
            document.getElementById('hasInstallments').checked = true;
            toggleInstallmentSection();
        @endif
    });

    document.getElementById('discount')?.addEventListener('input', updateSummary);
    document.getElementById('paidAmount')?.addEventListener('input', updateSummary);
</script>
@endpush

<style>
    .unit-card {
        transition: all 0.2s ease;
    }
    .unit-card:hover {
        transform: scale(1.03);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    }
    .list-group-item {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
</style>
@endsection