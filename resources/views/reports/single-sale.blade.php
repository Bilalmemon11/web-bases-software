@extends('layouts.app')
@section('title', 'Sale Report #' . $sale->id)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-receipt me-2"></i>Sale Report #{{ $sale->id }}</h3>
    <div>
        <a href="{{ route('sales.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Sales
        </a>
        <a href="{{ route('reports.sale.download', ['project' => $project->slug, 'sale' => $sale->id]) }}" 
           class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
    </div>
</div>

{{-- Sale Header Info --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">Sale Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Sale ID:</strong></td>
                            <td>#{{ $sale->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Sale Date:</strong></td>
                            <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Project:</strong></td>
                            <td>{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($sale->status == 'sold')
                                <span class="badge bg-danger">Sold</span>
                                @elseif($sale->status == 'reserved')
                                <span class="badge bg-warning">Reserved</span>
                                @else
                                <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="text-info mb-3">Client Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Name:</strong></td>
                            <td>{{ $sale->client->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $sale->client->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>CNIC:</strong></td>
                            <td>{{ $sale->client->cnic ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td>{{ $sale->client->address ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Financial Summary --}}
<section class="mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-secondary mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($sale->total_amount, 2) }}</h4>
                    <small class="text-muted">Gross Amount</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-tag fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($sale->discount, 2) }}</h4>
                    <small class="text-muted">Discount</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($sale->paid_amount, 2) }}</h4>
                    <small class="text-muted">Amount Paid</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($sale->pending_amount, 2) }}</h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Units Sold --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-home text-primary me-2"></i>Units Purchased
            </h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Unit No</th>
                            <th>Type</th>
                            <th>Size (sq ft)</th>
                            <th class="text-end">Unit Price</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->units as $unit)
                        <tr>
                            <td><strong>{{ $unit->unit_no }}</strong></td>
                            <td><span class="badge bg-info">{{ ucfirst($unit->type) }}</span></td>
                            <td>{{ $unit->size }}</td>
                            <td class="text-end">₨ {{ number_format($unit->pivot->unit_price, 2) }}</td>
                            <td>{{ $unit->location ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-end">₨ {{ number_format($sale->units->sum('pivot.unit_price'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Payment History --}}
<!-- <section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-money-bill-wave text-success me-2"></i>Payment History
            </h5>
            @if($sale->payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th>Payment Method</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                            <td class="text-end text-success"><strong>₨ {{ number_format($payment->amount, 2) }}</strong></td>
                            <td><span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span></td>
                            <td>{{ $payment->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td class="text-end">Total Paid:</td>
                            <td class="text-end text-success">₨ {{ number_format($sale->payments->sum('amount'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No payments recorded yet.
            </div>
            @endif
        </div>
    </div>
</section> -->

{{-- Financial Breakdown --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-calculator text-warning me-2"></i>Financial Breakdown
            </h5>
            <table class="table">
                <tr>
                    <td width="50%"><strong>Gross Sale Amount:</strong></td>
                    <td class="text-end">₨ {{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Discount Applied:</strong></td>
                    <td class="text-end text-warning">- ₨ {{ number_format($sale->discount, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <td><strong>Net Amount:</strong></td>
                    <td class="text-end"><strong>₨ {{ number_format($sale->net_amount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Amount Paid:</strong></td>
                    <td class="text-end text-success">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                </tr>
                <tr class="table-warning">
                    <td><strong>Outstanding Balance:</strong></td>
                    <td class="text-end text-danger"><strong>₨ {{ number_format($sale->pending_amount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Payment Progress:</strong></td>
                    <td class="text-end">
                        @php
                            $progress = $sale->net_amount > 0 ? ($sale->paid_amount / $sale->net_amount) * 100 : 0;
                        @endphp
                        <strong>{{ number_format($progress, 1) }}%</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</section>
@endsection