@extends('layouts.app')
@section('title', 'Sale #' . $sale->id . ' Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Sale #{{ $sale->id }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.edit', [$project->slug, $sale->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-edit me-1"></i> Edit Sale
        </a>
        <a href="{{ route('sales.index', $project->slug) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4">

    {{-- ── Left Column ──────────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Sale Info Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Sale Information</h5>
                <span class="badge fs-6 {{ $sale->status=='sold'?'bg-success':($sale->status=='reserved'?'bg-warning text-dark':'bg-secondary') }}">
                    {{ ucfirst($sale->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small">Client</label>
                        <p class="fw-semibold mb-0">{{ $sale->client->name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Sale Date</label>
                        <p class="fw-semibold mb-0">{{ $sale->sale_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Units</label>
                        <p class="fw-semibold mb-0">{{ $sale->units->pluck('unit_no')->join(', ') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Units Table --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-building text-secondary me-2"></i>Units Purchased</h5>
            </div>
            <div class="table-responsive card-body">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Unit No</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->units as $unit)
                        <tr>
                            <td class="fw-semibold">{{ $unit->unit_no }}</td>
                            <td>{{ ucfirst($unit->type) }}</td>
                            <td>{{ $unit->size ?? '—' }}</td>
                            <td class="text-end">₨ {{ number_format($unit->pivot->unit_price, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $unit->status=='sold'?'bg-success':($unit->status=='reserved'?'bg-warning text-dark':'bg-secondary') }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history text-success me-2"></i>Payment History</h5>
                @if($sale->remaining_amount > 0)
                <a href="{{ route('payments.create', [$project->slug, $sale->id]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i>Add Payment
                </a>
                @else
                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Fully Paid</span>
                @endif
            </div>
            <div class="table-responsive card-body">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Notes</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sale->payments as $i => $pmt)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ $pmt->payment_date->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                    {{ $pmt->method_label }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $pmt->reference }}</td>
                            <td class="text-muted small">{{ $pmt->notes ?? '—' }}</td>
                            <td class="text-end fw-semibold text-success">₨ {{ number_format($pmt->amount, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('payments.edit', [$project->slug, $sale->id, $pmt->id]) }}"
                                   class="btn btn-xs btn-outline-secondary py-0 px-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-xs btn-outline-danger py-0 px-2"
                                    onclick="deleteModal('deleteModal','{{ route('payments.destroy', [$project->slug, $sale->id, $pmt->id]) }}','Delete this payment of ₨ {{ number_format($pmt->amount,2) }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No payments recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($sale->payments->count())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total Received:</td>
                            <td class="text-end fw-bold text-success">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>

    {{-- ── Right Column: Financial Summary ──────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 0px;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-calculator text-primary me-2"></i>Financial Summary</h5>
            </div>
            <div class="card-body">

                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Gross Amount</span>
                    <span class="fw-semibold">₨ {{ number_format($sale->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Discount</span>
                    <span class="fw-semibold text-danger">— ₨ {{ number_format($sale->discount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Net Amount</span>
                    <span class="fw-bold">₨ {{ number_format($sale->net_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Total Paid</span>
                    <span class="fw-semibold text-success">₨ {{ number_format($sale->paid_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Outstanding</span>
                    <span class="fw-bold {{ $sale->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        ₨ {{ number_format($sale->remaining_amount, 2) }}
                    </span>
                </div>

                {{-- Progress --}}
                <div class="mt-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Payment Progress</span>
                        <span>{{ $sale->payment_progress }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ $sale->payment_progress }}%"
                             aria-valuenow="{{ $sale->payment_progress }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                @if($sale->remaining_amount > 0)
                <div class="mt-4">
                    <a href="{{ route('payments.create', [$project->slug, $sale->id]) }}"
                       class="btn btn-success w-100">
                        <i class="fas fa-plus-circle me-2"></i>Record New Payment
                    </a>
                </div>
                @else
                <div class="alert alert-success mt-4 mb-0 text-center">
                    <i class="fas fa-check-circle me-2"></i>Sale is fully paid!
                </div>
                @endif

                <div class="mt-2">
                    <a href="{{ route('sales.report', [$project->slug, $sale->id]) }}"
                       class="btn btn-outline-primary w-100" target="_blank">
                        <i class="fas fa-file-pdf me-2"></i>Print / Download Report
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection