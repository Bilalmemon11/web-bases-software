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
                            placeholder="Search units (unit no, type, size, status)..."
                            onkeyup="filterUnits()">
                    </div>
                    <div class="card-body h-100 overflow-y-auto">
                        <div class="row g-3">
                            @foreach($units as $unit)
                            @php
                            $isSelected = isset($sale) && in_array($unit->id, $sale->units->pluck('id')->toArray());
                            // ADD THIS LINE to check old input on validation failure
                            $isSelected = $isSelected || (old('units') && in_array($unit->id, old('units')));
                            $disabled = $unit->status === 'sold' && !$isSelected;
                            @endphp
                            <div class="col-lg-4 col-sm-6">
                                <div
                                    class="card unit-card {{ $isSelected ? 'border-primary bg-warning-subtle' : '' }} {{ $disabled ? 'opacity-50' : '' }}"
                                    style="cursor: pointer;"
                                    onclick="toggleUnit({{ $unit->id }}, {{ $unit->sale_price }}, '{{ $unit->unit_no }}', '{{ $unit->type }}', '{{ $unit->size }}', {{ $disabled ? 'true':'false' }})"
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
                                            <span class="badge bg-secondary p-1">{{ format_currency_unit($unit->sale_price, 2) }}</span><span class="small">₨ {{ number_format($unit->sale_price, 2) }}</span>
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

                        {{-- Payment Method --}}
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <input type="text" name="payment_method" class="form-control"
                                value="{{ old('payment_method', $sale->payment_method ?? '') }}">
                        </div>

                        {{-- Paid Amount --}}
                        <div class="mb-3">
                            <label class="form-label">Paid Amount</label>
                            <input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control"
                                value="{{ old('paid_amount', $sale->paid_amount ?? 0) }}"
                                oninput="currencyFormat(this,'paidDisplay')">
                            <small id="paidDisplay" class="d-block"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount (optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" step="0.01" name="discount" id="discount"
                                    class="form-control" value="{{ old('discount', $sale->discount ?? 0) }}"
                                    placeholder="e.g., 50000"
                                    oninput="currencyFormat(this, 'discount-display')">
                            </div>
                            <small id="discount-display" class="d-block text-muted"></small>
                        </div>
                        {{-- Summary --}}
                        <div class="border-top pt-3">
                            <p class="mb-1"><strong>Selected Units:</strong> <span id="selectedUnitsCount">0</span></p>
                            <p class="mb-1"><strong>Total Price:</strong> ₨ <span id="totalPriceDisplay">0.00</span></p>
                            <p class="mb-1"><strong>Net Total:</strong> ₨ <span id="netTotalDisplay">0.00</span></p>
                            <p><strong>Remaining:</strong> ₨ <span id="remainingDisplay">0.00</span></p>
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
@endsection

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
            selectedUnits.set(id, {
                price,
                unit_no
            });
            card.classList.add('border-primary', 'bg-warning-subtle');
        }
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        selectedUnits.forEach(u => total += parseFloat(u.price));

        const count = selectedUnits.size;
        const paid = parseFloat(document.getElementById('paidAmount')?.value || 0);
        const discount = parseFloat(document.getElementById('discount')?.value || 0);

        // Remaining = Total - Discount - Paid
        const remaining = total - discount - paid;
        const netTotal = total - discount;
        document.getElementById('netTotalDisplay').textContent = netTotal.toLocaleString('en-PK', {
            minimumFractionDigits: 2
        });

        // Update UI
        document.getElementById('selectedUnitsCount').textContent = count;
        document.getElementById('totalPriceDisplay').textContent = total.toLocaleString('en-PK', {
            minimumFractionDigits: 2
        });
        document.getElementById('remainingDisplay').textContent = remaining.toLocaleString('en-PK', {
            minimumFractionDigits: 2
        });

        // Update hidden units inputs
        const form = document.querySelector('form');
        // remove previous hidden inputs
        document.querySelectorAll('input[name="units[]"]').forEach(h => h.remove());

        selectedUnits.forEach((_, id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'units[]';
            input.value = id;
            form.appendChild(input);
        });
    }

    document.getElementById('discount')?.addEventListener('input', updateSummary);
    document.getElementById('paidAmount')?.addEventListener('input', updateSummary);

    // NEW: Initialize selected units from old input or existing sale
    function initializeSelectedUnits() {
        // Get all unit cards that are already marked as selected
        const preselectedCards = document.querySelectorAll('.unit-card.border-primary');

        preselectedCards.forEach(card => {
            const unitId = card.id.replace('unit-card-', '');
            const priceText = card.querySelector('.text-primary .small').textContent;
            const price = parseFloat(priceText.replace(/[^\d.-]/g, ''));
            const unitNo = card.querySelector('strong').textContent;

            selectedUnits.set(parseInt(unitId), {
                price: price,
                unit_no: unitNo
            });
        });

        updateSummary();
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('.select2').select2({
            width: '100%'
        });

        // Initialize selected units on page load
        initializeSelectedUnits();
    });

    function filterUnits() {
        let input = document.getElementById("unitSearch").value.toLowerCase();
        let unitCards = document.querySelectorAll(".unit-card");

        unitCards.forEach(card => {
            let text = card.innerText.toLowerCase();
            if (text.includes(input)) {
                card.parentElement.style.display = "";
            } else {
                card.parentElement.style.display = "none";
            }
        });
    }
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
</style>