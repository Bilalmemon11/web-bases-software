@extends('layouts.app')
@section('title', 'Overall Report - Junaid Builders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-chart-pie me-2"></i>Overall Project Report</h3>
    <div>
        <a href="{{ route('reports.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'overall']) }}" 
           class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
    </div>
</div>

{{-- Project Info --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-info-circle text-primary me-2"></i>Project Information
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Project Name:</th>
                            <td>{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status:</th>
                            <td>
                                <span class="badge bg-{{ $project->status == 'completed' ? 'success' : ($project->status == 'in_progress' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Start Date:</th>
                            <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M, Y') : '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Total Members:</th>
                            <td>{{ $members->count() }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Total Units:</th>
                            <td>{{ $units_count }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Clients:</th>
                            <td>{{ $clients_count }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @if($project->description)
            <div class="mt-3">
                <strong class="text-muted">Description:</strong>
                <p class="mb-0">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Financial Summary --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="fas fa-dollar-sign text-success me-2"></i>Financial Summary
            </h5>
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                        <small class="text-muted d-block">Total Investment</small>
                        <h4 class="mb-0">₨ {{ number_format($total_investment, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-danger bg-opacity-10 rounded">
                        <small class="text-muted d-block">Total Expenses</small>
                        <h4 class="mb-0">₨ {{ number_format($total_expenses, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-success bg-opacity-10 rounded">
                        <small class="text-muted d-block">Total Sales</small>
                        <h4 class="mb-0">₨ {{ number_format($total_sales, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-{{ $profit >= 0 ? 'success' : 'danger' }} bg-opacity-10 rounded">
                        <small class="text-muted d-block">Projected Profit</small>
                        <h4 class="mb-0 text-{{ $profit >= 0 ? 'success' : 'danger' }}">
                            ₨ {{ number_format($profit, 2) }}
                        </h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-info bg-opacity-10 rounded">
                        <small class="text-muted d-block">Amount Received</small>
                        <h4 class="mb-0">₨ {{ number_format($total_received, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                        <small class="text-muted d-block">Pending Payment</small>
                        <h4 class="mb-0">₨ {{ number_format($total_pending, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-secondary bg-opacity-10 rounded">
                        <small class="text-muted d-block">Total Discount</small>
                        <h4 class="mb-0">₨ {{ number_format($total_discount, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-3 bg-primary bg-opacity-10 rounded">
                        <small class="text-muted d-block">Progress</small>
                        <h4 class="mb-0">{{ number_format($progress, 1) }}%</h4>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Units Overview --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="fas fa-building text-warning me-2"></i>Units Overview
            </h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h2 class="text-primary mb-1">{{ $units_count }}</h2>
                        <small class="text-muted">Total Units</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h2 class="text-success mb-1">{{ $units_available }}</h2>
                        <small class="text-muted">Available</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h2 class="text-warning mb-1">{{ $units_reserved }}</h2>
                        <small class="text-muted">Reserved</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <h2 class="text-danger mb-1">{{ $units_sold }}</h2>
                        <small class="text-muted">Sold</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Members Summary --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-users text-info me-2"></i>Project Members
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th class="text-end">Investment</th>
                            <th class="text-end">Profit Share</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($member->pivot->role ?? 'Member') }}
                                </span>
                            </td>
                            <td class="text-end">₨ {{ number_format($member->pivot->investment_amount ?? 0, 2) }}</td>
                            <td class="text-end">{{ $member->pivot->profit_share ?? 0 }}%</td>
                            <td>{{ $member->phone }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No members found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Recent Activity --}}
<section>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-chart-line text-success me-2"></i>Recent Sales
                    </h5>
                    <p class="text-muted">Total Sales: <strong>{{ $sales_count }}</strong></p>
                    @if($sales_count > 0)
                    <small class="text-muted">Last sale recorded in the system</small>
                    @else
                    <p class="text-muted">No sales recorded yet</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-receipt text-danger me-2"></i>Total Expenses
                    </h5>
                    <p class="text-muted">Expense Records: <strong>{{ $expenses_count }}</strong></p>
                    @if($expenses_count > 0)
                    <small class="text-muted">All project expenses tracked</small>
                    @else
                    <p class="text-muted">No expenses recorded yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection