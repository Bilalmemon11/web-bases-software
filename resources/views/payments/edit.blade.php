@extends('layouts.app')
@section('title', 'Edit Payment')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fas fa-edit text-warning me-2"></i>Edit Payment</h4>
    <a href="{{ route('sales.show', [$project->slug, $sale->id]) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Sale
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-edit text-warning me-2"></i>Update Payment</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('sales.payments.update', [$project->slug, $sale->id, $payment->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('payments._form')
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-warning px-4">
                            <i class="fas fa-save me-2"></i>Update Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-receipt text-primary me-2"></i>Sale Summary</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Client:</strong> {{ $sale->client->name }}</p>
                <p class="mb-1"><strong>Net Amount:</strong> ₨ {{ number_format($sale->net_amount, 2) }}</p>
                <p class="mb-1 text-success"><strong>Total Paid:</strong> ₨ {{ number_format($sale->paid_amount, 2) }}</p>
                <p class="mb-0 text-danger fw-bold"><strong>Outstanding:</strong> ₨ {{ number_format($sale->remaining_amount, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection