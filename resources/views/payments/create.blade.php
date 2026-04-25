@extends('layouts.app')
@section('title', 'Add Payment — Sale #' . $sale->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fas fa-money-bill-wave text-success me-2"></i>Add Payment — Sale #{{ $sale->id }}</h4>
    <a href="{{ route('sales.show', [$project->slug, $sale->id]) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Sale
    </a>
</div>

<div class="row g-4">

    {{-- ── Payment Form ───────────────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle text-success me-2"></i>New Payment</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('payments.store', [$project->slug, $sale->id]) }}" method="POST">
                    @csrf
                    @include('payments._form')
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-success px-4">
                            <i class="fas fa-save me-2"></i>Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Sale Summary Sidebar ──────────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-receipt text-primary me-2"></i>Sale Summary</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Client:</strong> {{ $sale->client->name }}</p>
                <p class="mb-1"><strong>Units:</strong> {{ $sale->units->pluck('unit_no')->join(', ') }}</p>
                <p class="mb-1"><strong>Sale Date:</strong> {{ $sale->sale_date?->format('d M Y') }}</p>
                <hr>
                <p class="mb-1"><strong>Gross Amount:</strong> ₨ {{ number_format($sale->total_amount, 2) }}</p>
                <p class="mb-1"><strong>Discount:</strong> ₨ {{ number_format($sale->discount, 2) }}</p>
                <p class="mb-1"><strong>Net Amount:</strong> ₨ {{ number_format($sale->net_amount, 2) }}</p>
                <p class="mb-1 text-success"><strong>Total Paid:</strong> ₨ {{ number_format($sale->paid_amount, 2) }}</p>
                <p class="mb-0 text-danger fw-bold"><strong>Outstanding:</strong> ₨ {{ number_format($sale->remaining_amount, 2) }}</p>

                {{-- Progress bar --}}
                <div class="mt-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Payment Progress</span>
                        <span>{{ $sale->payment_progress }}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $sale->payment_progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment History --}}
        @if($sale->payments->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-history text-muted me-2"></i>Payment History</h6>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($sale->payments as $pmt)
                <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                    <div>
                        <div class="fw-semibold">₨ {{ number_format($pmt->amount, 2) }}</div>
                        <small class="text-muted">{{ $pmt->payment_date->format('d M Y') }} &middot; {{ $pmt->method_label }}</small>
                        @if($pmt->reference !== '—')
                            <br><small class="text-secondary">{{ $pmt->reference }}</small>
                        @endif
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle mt-1">Paid</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

</div>
@endsection