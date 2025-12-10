@extends('layouts.app')

@section('title', $client->name . ' - Client Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="fas fa-user text-primary me-2"></i>{{ $client->name }}</h3>
    <a href="{{ route('clients.index', $project->slug) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Clients
    </a>
</div>

<section class="row g-3 mb-4">
    <!-- Client Info -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light fw-bold"><i class="fas fa-id-card me-2 text-primary"></i>Client Information</div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $client->name }}</p>
                <p class="mb-2"><strong>Phone:</strong> {{ $client->phone ?? '—' }}</p>
                <p class="mb-2"><strong>CNIC:</strong> {{ $client->cnic ?? '—' }}</p>
                <p class="mb-2"><strong>Address:</strong> {{ $client->address ?? '—' }}</p>
                <p class="mb-0"><strong>Notes:</strong> {{ $client->notes ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="col-md-6 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light fw-bold"><i class="fas fa-chart-pie me-2 text-success"></i>Activity Summary</div>
            <div class="card-body row text-center">
                <div class="col-6 col-md-4 mb-3">
                    <small class="text-muted d-block">Total Purchases</small>
                    <h5 class="fw-bold text-primary">₨ {{ number_format($summary['total_sales'], 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <small class="text-muted d-block">Discounts</small>
                    <h5 class="fw-bold text-warning">₨ {{ number_format($summary['total_discount'], 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <small class="text-muted d-block">Amount Paid</small>
                    <h5 class="fw-bold text-success">₨ {{ number_format($summary['total_paid'], 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <small class="text-muted d-block">Pending</small>
                    <h5 class="fw-bold text-danger">₨ {{ number_format($summary['total_pending'], 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <small class="text-muted d-block">Total Units</small>
                    <h5 class="fw-bold">{{ $summary['total_units'] }}</h5>
                </div>
                <div class="col-6 col-md-4 mb-3">
                    <small class="text-muted d-block">Avg. Unit Price</small>
                    <h5 class="fw-bold text-secondary">₨ {{ number_format($summary['avg_unit_price'], 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Units Purchased -->
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-bold">
            <i class="fas fa-home text-secondary me-2"></i>Purchased / Reserved Units
        </div>
        <div class="card-body">
            @php
            $units = $client->sales->flatMap->units->unique('id');
            @endphp

            @if($units->isEmpty())
            <p class="text-muted mb-0">No units associated with this client.</p>
            @else
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Unit No</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th class="text-end">Price</th>
                            <th>Status</th>
                            <th>Sale Date</th>
                            <th>View Sale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $index => $unit)
                        @php
                        $sale = $client->sales->firstWhere('id', $unit->pivot->sale_id);
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $unit->unit_no }}</td>
                            <td>{{ ucfirst($unit->type) }}</td>
                            <td>{{ $unit->size ?? '—' }}</td>
                            <td class="text-end">₨ {{ number_format($unit->pivot->unit_price, 2) }}</td>
                            <td>
                                <span class="badge 
                                            {{ $sale->status === 'sold' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td>{{ $sale->sale_date?->format('d-M-Y') ?? '—' }}</td>
                            <td>
                                <a class="btn btn-sm btn-info" href="{{ route('sales.show', [session('active_project_slug'),$sale->id]) }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection