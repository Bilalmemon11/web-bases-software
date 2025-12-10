@extends('layouts.app')
@section('title', 'Clients Report - Junaid Builders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-user-tie me-2"></i>Clients Report</h3>
    <div>
        <a href="{{ route('reports.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'clients']) }}" 
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
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h3 class="mb-1">{{ $clientStats->count() }}</h3>
                    <small class="text-muted">Total Clients</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                    <h3 class="mb-1">₨ {{ number_format($clientStats->sum('total_sales'), 2) }}</h3>
                    <small class="text-muted">Total Sales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                    <h3 class="mb-1">₨ {{ number_format($clientStats->sum('total_paid'), 2) }}</h3>
                    <small class="text-muted">Total Paid</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h3 class="mb-1">₨ {{ number_format($clientStats->sum('total_pending'), 2) }}</h3>
                    <small class="text-muted">Pending Amount</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Clients Table --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-list text-secondary me-2"></i>Client Details
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>CNIC</th>
                            <th class="text-center">Units Purchased</th>
                            <th class="text-end">Total Sales</th>
                            <th class="text-end">Amount Paid</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Pending</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientStats as $index => $stat)
                        @php
                            $client = $stat['client'];
                            $paymentProgress = $stat['total_sales'] > 0 ? ($stat['total_paid'] / $stat['total_sales']) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $client->name }}</strong></td>
                            <td>{{ $client->phone ?? '—' }}</td>
                            <td>{{ $client->cnic ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $stat['units_count'] }}</span>
                            </td>
                            <td class="text-end">₨ {{ number_format($stat['total_sales'], 2) }}</td>
                            <td class="text-end text-success">₨ {{ number_format($stat['total_paid'], 2) }}</td>
                            <td class="text-end text-warning">₨ {{ number_format($stat['total_discount'], 2) }}</td>
                            <td class="text-end text-danger">₨ {{ number_format($stat['total_pending'], 2) }}</td>
                            <td class="text-center">
                                @if($paymentProgress >= 100)
                                <span class="badge bg-success">Paid</span>
                                @elseif($paymentProgress > 0)
                                <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No clients found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Total:</td>
                            <td class="text-center">{{ $clientStats->sum('units_count') }}</td>
                            <td class="text-end">₨ {{ number_format($clientStats->sum('total_sales'), 2) }}</td>
                            <td class="text-end">₨ {{ number_format($clientStats->sum('total_paid'), 2) }}</td>
                            <td class="text-end">₨ {{ number_format($clientStats->sum('total_pending'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Payment Progress --}}
@if($clientStats->count() > 0)
<section class="mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-line text-success me-2"></i>Client Payment Progress
            </h5>
            @foreach($clientStats as $stat)
            @php
                $client = $stat['client'];
                $progress = $stat['total_sales'] > 0 ? ($stat['total_paid'] / $stat['total_sales']) * 100 : 0;
            @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small><strong>{{ $client->name }}</strong></small>
                    <small class="text-muted">{{ number_format($progress, 1) }}% Paid</small>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar bg-{{ $progress >= 100 ? 'success' : ($progress > 0 ? 'warning' : 'danger') }}" 
                         role="progressbar" 
                         style="width: {{ $progress }}%">
                        ₨ {{ number_format($stat['total_paid'], 0) }} / ₨ {{ number_format($stat['total_sales'], 0) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection