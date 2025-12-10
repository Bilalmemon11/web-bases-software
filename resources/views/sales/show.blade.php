@extends('layouts.app')
@section('title','Sale Details')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Sale Details</h4>
    <a href="{{ route('sales.index', $project->slug) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Sales
    </a>
</div>
<section>
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="m-0">Sale #{{ $sale->id }}</h5>
            <a href="{{ route('sales.edit', [$project->slug,$sale->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        </div>
        <div class="card-body">
            <p><strong>Client:</strong> {{ $sale->client->name }}</p>
            <p><strong>Status:</strong>
                <span class="badge {{ $sale->status=='sold'?'bg-success':($sale->status=='reserved'?'bg-warning text-dark':'bg-secondary') }}">
                    {{ ucfirst($sale->status) }}
                </span>
            </p>
            <p><strong>Payment Method:</strong> {{ $sale->payment_method ?? '—' }}</p>
            <p><strong>Sale Date:</strong> {{ $sale->sale_date?->format('d-M-Y') }}</p>
            <p><strong>Total Amount:</strong> ₨ {{ number_format($sale->total_amount,2) }}</p>
            <p><strong>Paid Amount:</strong> ₨ {{ number_format($sale->paid_amount,2) }}</p>
            <p><strong>Discount:</strong> ₨ {{ number_format($sale->discount,2) }}</p>
            <p><strong>Remaining Amount:</strong> ₨ {{ number_format($sale->remaining_amount,2) }}</p>

            <h6 class="mt-4">Units in this Sale:</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Unit No</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->units as $unit)
                        <tr>
                            <td>{{ $unit->unit_no }}</td>
                            <td>{{ $unit->type }}</td>
                            <td>₨ {{ number_format($unit->pivot->unit_price,2) }}</td>
                            <td>
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
    </div>
</section>
@endsection