@extends('layouts.app')
@section('title', 'Sales Report - Junaid Builders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-money-bill-wave me-2"></i>Sales Report</h3>
    <div>
        <a href="{{ route('reports.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'sales']) }}" 
           class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<section class="mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                    <h3 class="mb-1">{{ $stats['total_sales'] }}</h3>
                    <small class="text-muted">Total Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_amount'], 2) }}</h4>
                    <small class="text-muted">Total Amount</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_received'], 2) }}</h4>
                    <small class="text-muted">Amount Received</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_pending'], 2) }}</h4>
                    <small class="text-muted">Pending Amount</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x text-secondary mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_discount'], 2) }}</h4>
                    <small class="text-muted">Total Discounts Given</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_amount'] - $stats['total_discount'], 2) }}</h4>
                    <small class="text-muted">Net Sales Amount</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Sales by Status --}}
@if($stats['by_status']->count() > 0)
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-bar text-primary me-2"></i>Sales by Status
            </h5>
            <div class="row">
                @foreach($stats['by_status'] as $status => $count)
                @php
                    $percentage = $stats['total_sales'] > 0 ? ($count / $stats['total_sales']) * 100 : 0;
                    $colors = ['sold' => 'danger', 'reserved' => 'warning', 'pending' => 'secondary'];
                @endphp
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <h3 class="text-{{ $colors[$status] ?? 'primary' }}">{{ $count }}</h3>
                        <small class="text-muted">{{ ucfirst($status) }} ({{ number_format($percentage, 1) }}%)</small>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-{{ $colors[$status] ?? 'primary' }}" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Sales by Month --}}
@if($stats['by_month']->count() > 0)
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-line text-success me-2"></i>Monthly Sales Trend
            </h5>
            @foreach($stats['by_month'] as $month => $amount)
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small><strong>{{ $month }}</strong></small>
                    <small class="text-muted">₨ {{ number_format($amount, 2) }}</small>
                </div>
                <div class="progress" style="height: 20px;">
                    @php
                        $maxAmount = $stats['by_month']->max();
                        $barWidth = $maxAmount > 0 ? ($amount / $maxAmount) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: {{ $barWidth }}%">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Sales Table --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-list text-secondary me-2"></i>Sales Details
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Sale Date</th>
                            <th>Client</th>
                            <th>Units</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Net Amount</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Pending</th>
                            <th class="text-center">Status</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sale->sale_date->format('d-M-Y') }}</td>
                            <td><strong>{{ $sale->client->name }}</strong></td>
                            <td>
                                <small>
                                    @foreach($sale->units as $unit)
                                        <span class="badge bg-secondary">{{ $unit->unit_no }}</span>
                                    @endforeach
                                </small>
                            </td>
                            <td class="text-end">₨ {{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end text-danger">₨ {{ number_format($sale->discount, 2) }}</td>
                            <td class="text-end"><strong>₨ {{ number_format($sale->net_amount, 2) }}</strong></td>
                            <td class="text-end text-success">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-end text-warning">₨ {{ number_format($sale->pending_amount, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $sale->status == 'sold' ? 'danger' : ($sale->status == 'reserved' ? 'warning text-dark' : 'secondary') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($sale->payment_method ?? '—') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">No sales found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-end">₨ {{ number_format($stats['total_amount'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($stats['total_discount'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($stats['total_amount'] - $stats['total_discount'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($stats['total_received'], 2) }}</td>
                            <td class="text-end">₨ {{ number_format($stats['total_pending'], 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Payment Progress --}}
<section class="mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-tasks text-info me-2"></i>Overall Payment Collection Progress
            </h5>
            @php
                $collectionRate = $stats['total_amount'] > 0 ? ($stats['total_received'] / $stats['total_amount']) * 100 : 0;
            @endphp
            <div class="progress" style="height: 40px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $collectionRate }}%">
                    {{ number_format($collectionRate, 1) }}% Collected
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">Received: ₨ {{ number_format($stats['total_received'], 0) }}</small>
                <small class="text-muted">Pending: ₨ {{ number_format($stats['total_pending'], 0) }}</small>
            </div>
        </div>
    </div>
</section>
@endsection