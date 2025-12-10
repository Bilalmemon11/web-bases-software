@extends('layouts.app')
@section('title', 'Units Report - Junaid Builders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-building me-2"></i>Units Report</h3>
    <div>
        <a href="{{ route('reports.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'units']) }}" 
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
                    <i class="fas fa-building fa-2x text-primary mb-2"></i>
                    <h3 class="mb-1">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total Units</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="mb-1">{{ $stats['available'] }}</h3>
                    <small class="text-muted">Available</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-bookmark fa-2x text-warning mb-2"></i>
                    <h3 class="mb-1">{{ $stats['reserved'] }}</h3>
                    <small class="text-muted">Reserved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-home fa-2x text-danger mb-2"></i>
                    <h3 class="mb-1">{{ $stats['sold'] }}</h3>
                    <small class="text-muted">Sold</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Financial Summary --}}
<section class="mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_value'], 2) }}</h4>
                    <small class="text-muted">Total Inventory Value</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['sold_value'], 2) }}</h4>
                    <small class="text-muted">Sold Units Value</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Units by Type --}}
@if($stats['by_type']->count() > 0)
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-layer-group text-primary me-2"></i>Units by Type
            </h5>
            <div class="row">
                @foreach($stats['by_type'] as $type => $count)
                @php
                    $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small><strong>{{ ucfirst($type) }}</strong></small>
                        <small class="text-muted">{{ $count }} units ({{ number_format($percentage, 1) }}%)</small>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                            {{ $count }} Units
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Units Table --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-list text-secondary me-2"></i>Unit Details
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Unit No</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Status</th>
                            <th>Client</th>
                            <th>Sale Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units->groupBy('type') as $type => $groupedUnits)
                        {{-- Group Header Row --}}
                        <tr class="table-light border-top">
                            <td colspan="8" class="fw-semibold text-primary">
                                <i class="fas fa-layer-group me-2"></i> {{ strtoupper($type) }} ({{ $groupedUnits->count() }} units)
                            </td>
                        </tr>
                        {{-- Units Under This Type --}}
                        @foreach($groupedUnits as $unit)
                        <tr>
                            <td>{{ $loop->parent->index + $loop->index + 1 }}</td>
                            <td><strong>{{ $unit->unit_no }}</strong></td>
                            <td>{{ ucfirst($unit->type) }}</td>
                            <td>{{ $unit->size }}</td>
                            <td class="text-end">₨ {{ number_format($unit->sale_price, 2) }}</td>
                            <td class="text-center">
                                <span class="badge 
                                    {{ $unit->status == 'sold' ? 'bg-danger' : 
                                       ($unit->status == 'reserved' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </td>
                            <td>{{ $unit->soldTo()?->name ?? $unit->reservedBy()?->name ?? '—' }}</td>
                            <td>{{ $unit->soldSale()?->sale_date?->format('d-M-Y') ?? $unit->reservedSale()?->sale_date?->format('d-M-Y') ?? '—' }}</td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No units found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Status Distribution --}}
<section class="mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-pie text-warning me-2"></i>Status Distribution
            </h5>
            <div class="row">
                @php
                    $statusData = [
                        ['name' => 'Available', 'count' => $stats['available'], 'color' => 'success'],
                        ['name' => 'Reserved', 'count' => $stats['reserved'], 'color' => 'warning'],
                        ['name' => 'Sold', 'count' => $stats['sold'], 'color' => 'danger']
                    ];
                @endphp
                @foreach($statusData as $status)
                @php
                    $percentage = $stats['total'] > 0 ? ($status['count'] / $stats['total']) * 100 : 0;
                @endphp
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $status['color'] }}" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <h4 class="mb-1">{{ $status['count'] }}</h4>
                        <small class="text-muted">{{ $status['name'] }} ({{ number_format($percentage, 1) }}%)</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection