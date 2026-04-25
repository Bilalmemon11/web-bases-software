@extends('layouts.app')
@section('title', isset($sale) ? 'Edit Sale' : 'Create Sale')

@section('content')
<section>
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ isset($sale) ? 'Edit Sale #' . $sale->id : 'Create New Sale' }}</h4>
        <a href="{{ route('sales.index', $project->slug) }}" class="btn btn-secondary">Back to Sales</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form
        action="{{ isset($sale) ? route('sales.update', [$project->slug, $sale->id]) : route('sales.store', $project->slug) }}"
        method="POST"
        class="needs-validation h-100" novalidate>
        @csrf
        @if(isset($sale)) @method('PUT') @endif

        <div class="row g-4 h-100">

            {{-- ── LEFT: Unit Selection ────────────────────────────────── --}}
            <div class="col-lg-8 h-100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light justify-content-between d-flex align-items-center">
                        <h5 class="mb-0"><i class="fas fa-building text-primary me-2"></i>Select Units</h5>
                        <input type="text" id="unitSearch" class="form-control form-control-sm"
                               style="max-width: 200px;"
                               placeholder="Search units..."
                               onkeyup="filterUnits()">
                    </div>
                    <div class="card-body h-100 overflow-y-auto">
                        <div class="row g-3">
                            @foreach($units as $unit)
                            @php
                                $isSelected = isset($sale) && in_array($unit->id, $sale->units->pluck('id')->toArray());
                                $isSelected = $isSelected || (old('units') && in_array($unit->id, old('units')));
                                $disabled   = $unit->status === 'sold' && !$isSelected;
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
                                            <span class="badge p-1 {{ $unit->status == 'available' ? 'bg-success' : ($unit->status == 'reserved' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ ucfirst($unit->status) }}
                                            </span>
                                        </div>
                                        <p class="mb-1 text-muted small"><i class="fas fa-home me-1"></i> {{ ucfirst($unit->type) }}</p>
                                        <p class="mb-1 text-muted small"><i class="fas fa-ruler-combined me-1"></i> {{ $unit->size ?? '—' }}</p>
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

            {{-- ── RIGHT: Sale Summary ─────────────────────────────────── --}}
            <div class="col-lg-4">

                {{-- Sale Details Card --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-receipt text-success me-2"></i>Sale Details</h5>
                    </div>
                    <div class="card-body">

                        {{-- Client --}}
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-danger">*</span></label>
                            <select name="client_id" class="form-select select2" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $sale->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="saleStatus" class="form-select" required>
                                <option value="reserved" {{ old('status', $sale->status ?? '') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                                <option value="sold"     {{ old('status', $sale->status ?? '') == 'sold'     ? 'selected' : '' }}>Sold</option>
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
                            <label class="form-label">Discount <span class="text-muted small">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" step="0.01" min="0" name="discount" id="discount"
                                    class="form-control"
                                    value="{{ old('discount', $sale->discount ?? 0) }}"
                                    placeholder="0.00"
                                    oninput="updateSummary();currencyFormat(this, 'discount-display')">
                            </div>
                            <small id="discount-display" class="d-block text-muted"></small>
                        </div>

                        {{-- Summary --}}
                        <div class="border-top pt-3 mb-0">
                            <p class="mb-1 d-flex justify-content-between">
                                <span><strong>Selected Units:</strong></span>
                                <span id="selectedUnitsCount">0</span>
                            </p>
                            <p class="mb-1 d-flex justify-content-between">
                                <span><strong>Total Price:</strong></span>
                                <span>₨ <span id="totalPriceDisplay">0.00</span></span>
                            </p>
                            <p class="mb-1 d-flex justify-content-between">
                                <span><strong>Net Total:</strong></span>
                                <span>₨ <span id="netTotalDisplay">0.00</span></span>
                            </p>
                        </div>

                        {{-- Hidden units inputs --}}
                        {{-- (injected dynamically by JS) --}}

                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success px-4" type="submit">
                        <i class="fas fa-save me-2"></i>{{ isset($sale) ? 'Update Sale' : 'Create Sale' }}
                    </button>
                </div>

            </div>{{-- end col-lg-4 --}}
        </div>{{-- end row --}}
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
            selectedUnits.set(id, { price, unit_no });
            card.classList.add('border-primary', 'bg-warning-subtle');
        }
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        selectedUnits.forEach(u => total += parseFloat(u.price));

        const discount = parseFloat(document.getElementById('discount')?.value || 0);
        const netTotal = Math.max(0, total - discount);

        const initialAmountEl = document.getElementById('initialAmount');
        const paid    = parseFloat(initialAmountEl?.value || 0);
        const remaining = Math.max(0, netTotal - paid);

        // Main summary
        document.getElementById('selectedUnitsCount').textContent = selectedUnits.size;
        document.getElementById('totalPriceDisplay').textContent  = total.toLocaleString('en-PK', { minimumFractionDigits: 2 });
        document.getElementById('netTotalDisplay').textContent    = netTotal.toLocaleString('en-PK', { minimumFractionDigits: 2 });

        // Rebuild hidden unit inputs
        document.querySelectorAll('input[name="units[]"]').forEach(h => h.remove());
        const form = document.querySelector('form');
        selectedUnits.forEach((_, id) => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'units[]';
            input.value = id;
            form.appendChild(input);
        });
    }

    function initializeSelectedUnits() {
        document.querySelectorAll('.unit-card.border-primary').forEach(card => {
            const unitId    = parseInt(card.id.replace('unit-card-', ''));
            const priceText = card.querySelector('.text-primary .small').textContent;
            const price     = parseFloat(priceText.replace(/[^\d.-]/g, ''));
            const unitNo    = card.querySelector('strong').textContent;
            selectedUnits.set(unitId, { price, unit_no: unitNo });
        });
        updateSummary();
    }

    function filterUnits() {
        const input = document.getElementById("unitSearch").value.toLowerCase();
        document.querySelectorAll(".unit-card").forEach(card => {
            card.parentElement.style.display = card.innerText.toLowerCase().includes(input) ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        $('.select2').select2({ width: '100%' });
        initializeSelectedUnits();
        toggleInitialMethodFields();

        document.getElementById('discount')?.addEventListener('input', updateSummary);
        document.getElementById('initialAmount')?.addEventListener('input', updateSummary);
    });
</script>
@endpush

<style>
    .unit-card { transition: all 0.2s ease; }
    .unit-card:hover { transform: scale(1.03); box-shadow: 0 0 10px rgba(0,0,0,.15); }
</style>