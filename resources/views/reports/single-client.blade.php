@extends('layouts.app')
@section('title', 'Client Report - ' . $client->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-user me-2"></i>Client Report: {{ $client->name }}</h3>
    <div>
        <a href="{{ route('clients.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Clients
        </a>
        <a href="{{ route('reports.client.download', ['project' => $project->slug, 'client' => $client->id]) }}" 
           class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
    </div>
</div>

{{-- Client Information --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-info-circle text-primary me-2"></i>Client Information
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Name:</strong></td>
                            <td>{{ $client->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $client->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>CNIC:</strong></td>
                            <td>{{ $client->cnic ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Address:</strong></td>
                            <td>{{ $client->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Project:</strong></td>
                            <td>{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Notes:</strong></td>
                            <td>{{ $client->notes ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Summary Cards --}}
<section class="mb-4">
    <div class="row g-3">
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1">{{ $summary['sales_count'] }}</h4>
                    <small class="text-muted">Total Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-home fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">{{ $summary['total_units'] }}</h4>
                    <small class="text-muted">Units Purchased</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-secondary mb-2"></i>
                    <h5 class="mb-1">₨ {{ number_format($summary['total_sales'], 0) }}</h5>
                    <small class="text-muted">Gross Amount</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-tag fa-2x text-warning mb-2"></i>
                    <h5 class="mb-1">₨ {{ number_format($summary['total_discount'], 0) }}</h5>
                    <small class="text-muted">Discount</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h5 class="mb-1">₨ {{ number_format($summary['total_paid'], 0) }}</h5>
                    <small class="text-muted">Amount Paid</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-danger mb-2"></i>
                    <h5 class="mb-1">₨ {{ number_format($summary['total_pending'], 0) }}</h5>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Sales History --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-history text-secondary me-2"></i>Sales History
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sale Date</th>
                            <th>Units</th>
                            <th class="text-end">Gross Amount</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Net Amount</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Pending</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->sales as $sale)
                        <tr>
                            <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                            <td>
                                @foreach($sale->units as $unit)
                                <span class="badge bg-secondary">{{ $unit->unit_no }}</span>
                                @endforeach
                            </td>
                            <td class="text-end">₨ {{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end text-warning">₨ {{ number_format($sale->discount, 2) }}</td>
                            <td class="text-end"><strong>₨ {{ number_format($sale->net_amount, 2) }}</strong></td>
                            <td class="text-end text-success">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-end text-danger">₨ {{ number_format($sale->pending_amount, 2) }}</td>
                            <td class="text-center">
                                @if($sale->status == 'sold')
                                <span class="badge bg-danger">Sold</span>
                                @elseif($sale->status == 'reserved')
                                <span class="badge bg-warning">Reserved</span>
                                @else
                                <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('reports.sale', ['project' => $project->slug, 'sale' => $sale->id]) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No sales found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-end">₨ {{ number_format($summary['total_sales'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($summary['total_discount'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($summary['net_amount'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($summary['total_paid'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($summary['total_pending'], 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Payment Progress --}}
@if($summary['net_amount'] > 0)
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-line text-success me-2"></i>Payment Progress
            </h5>
            @php
                $paymentProgress = ($summary['total_paid'] / $summary['net_amount']) * 100;
            @endphp
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success" role="progressbar" 
                     style="width: {{ $paymentProgress }}%"
                     aria-valuenow="{{ $paymentProgress }}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    {{ number_format($paymentProgress, 1) }}% Paid
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">Paid: ₨ {{ number_format($summary['total_paid'], 2) }}</small>
                <small class="text-muted">Pending: ₨ {{ number_format($summary['total_pending'], 2) }}</small>
            </div>
        </div>
    </div>
</section>
@endif
@endsection