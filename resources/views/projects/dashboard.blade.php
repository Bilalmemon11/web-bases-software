@extends('layouts.app')

@section('title', 'Dashboard - Junaid Builders')

@section('content')
<h3 class="mb-4">Project Overview</h3>
<section class="row g-3 mb-4">

    <!-- Total Investment -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-money-bill-wave fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['total_investment']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Total Investment</small>
                <h3 class="mb-0 fw-bold">₨ {{ $stats['total_investment'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Land Cost -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-map-marked-alt fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['land_cost']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Land Cost</small>
                <h3 class="mb-0 fw-bold">₨ {{ $stats['land_cost'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Total Expenses -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-calculator fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['total_expenses']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Total Expenses</small>
                <h3 class="mb-0 fw-bold">₨ {{ $stats['total_expenses'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Total Sales -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-chart-line fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['total_sales']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Total Sales</small>
                <h3 class="mb-0 fw-bold">₨ {{ $stats['total_sales'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Current Balance -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3 justify-content-between">
                    <div class="rounded p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-balance-scale fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['current_balance']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Current Balance</small>
                <h3 class="mb-0 fw-bold {{ $stats['current_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                    ₨ {{ $stats['current_balance'] }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Total Members -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded p-3 bg-secondary bg-opacity-10 text-secondary">
                        <i class="fas fa-users fs-4"></i>
                    </div>
                </div>
                <small class="text-muted d-block">Total Members</small>
                <h3 class="mb-0 fw-bold">{{ $stats['total_members'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Total Units -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-home fs-4"></i>
                    </div>
                </div>
                <small class="text-muted d-block">Total Units</small>
                <h3 class="mb-0 fw-bold">{{ $stats['total_units'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Units Sold -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-check-circle fs-4"></i>
                    </div>
                </div>
                <small class="text-muted d-block">Units Sold</small>
                <h3 class="mb-0 fw-bold">{{ $stats['units_sold'] }}</h3>
            </div>
        </div>
    </div>
    <!-- Units Reserved -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock fs-4"></i>
                    </div>
                </div>
                <small class="text-muted d-block">Units Reserved</small>
                <h3 class="mb-0 fw-bold">{{ $stats['units_reserved'] }}</h3>
            </div>
        </div>
    </div>

</section>
<!-- Financial Overview -->
<h4 class="mt-5 mb-3">Financial Overview</h4>
<section class="row g-3">

    <!-- Total Received -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-hand-holding-usd fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['total_received']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Total Received</small>
                <h3 class="mb-0 fw-bold text-success">₨ {{ $stats['total_received'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-clock fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['total_pending']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Pending Payments</small>
                <h3 class="mb-0 fw-bold text-danger">₨ {{ $stats['total_pending'] }}</h3>
            </div>
        </div>
    </div>
    <!-- Discounts Given -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-secondary bg-opacity-10 text-secondary">
                        <i class="fas fa-tag fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($stats['discounts_given']) }}
                    </small>
                </div>
                <small class="text-muted d-block">Total Discounts</small>
                <h3 class="mb-0 fw-bold text-secondary">₨ {{ $stats['discounts_given'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Profit -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-coins fs-4"></i>
                    </div>
                    <small class="fw-bold text-secondary badge bg-light">
                        {{ format_currency_unit($project->profit) }}
                    </small>
                </div>
                <small class="text-muted d-block">Projected Profit</small>
                <h3 class="mb-0 fw-bold text-info">₨ {{ number_format($project->profit, 0) }}</h3>
            </div>
        </div>
    </div>

    <!-- ROI -->
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-percentage fs-4"></i>
                    </div>
                </div>
                <small class="text-muted d-block">ROI (Return on Investment)</small>
                @php
                $roi = $stats['total_investment'] > 0
                ? round(($project->profit / $stats['total_investment']) * 100, 2)
                : 0;
                @endphp
                <h3 class="mb-0 fw-bold {{ $roi > 20 ? 'text-success' : ($roi > 10 ? 'text-warning' : 'text-danger') }}">
                    {{ $roi }}%
                </h3>
            </div>
        </div>
    </div>
</section>

<!-- Progress Indicators -->
<h4 class="mt-5 mb-3">Performance Indicators</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <small class="text-muted">Sales Progress ({{ $project->progress }}%)</small>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-success" style="width: {{ $project->progress }}%"></div>
            </div>
        </div>

        @php
        $collectionProgress = $stats['total_sales'] > 0
        ? round(($stats['total_received'] / $stats['total_sales']) * 100, 2)
        : 0;
        @endphp
        <div class="mb-3">
            <small class="text-muted">Collection Progress ({{ $collectionProgress }}%)</small>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-info" style="width: {{ $collectionProgress }}%"></div>
            </div>
        </div>

        <div>
            <small class="text-muted">Profit Margin ({{ $roi }}%)</small>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-warning" style="width: {{ $roi }}%"></div>
            </div>
        </div>
    </div>
</div>

@endsection