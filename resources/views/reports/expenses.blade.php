@extends('layouts.app')
@section('title', 'Expenses Report - Junaid Builders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-receipt me-2"></i>Expenses Report</h3>
    <div>
        <a href="{{ route('reports.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'expenses']) }}" 
           class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<section class="mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice-dollar fa-2x text-primary mb-2"></i>
                    <h3 class="mb-1">{{ $expenses->count() }}</h3>
                    <small class="text-muted">Total Expense Records</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-landmark fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['land_cost'], 2) }}</h4>
                    <small class="text-muted">Land Cost</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-tools fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($stats['total_expenses'], 2) }}</h4>
                    <small class="text-muted">Operational Expenses</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Grand Total --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm bg-danger bg-opacity-10">
        <div class="card-body text-center">
            <h5 class="card-title text-danger">
                <i class="fas fa-calculator me-2"></i>Total Project Expenses
            </h5>
            <h2 class="text-danger mb-0">₨ {{ number_format($stats['grand_total'], 2) }}</h2>
            <small class="text-muted">(Land Cost + Operational Expenses)</small>
        </div>
    </div>
</section>

{{-- Expenses by Category --}}
@if($stats['by_category']->count() > 0)
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-pie text-primary me-2"></i>Expenses by Category
            </h5>
            <div class="row">
                @foreach($stats['by_category'] as $category => $amount)
                @php
                    $percentage = $stats['total_expenses'] > 0 ? ($amount / $stats['total_expenses']) * 100 : 0;
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small><strong>{{ ucfirst($category) }}</strong></small>
                        <small class="text-muted">₨ {{ number_format($amount, 2) }} ({{ number_format($percentage, 1) }}%)</small>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- Monthly Expenses Trend --}}
@if($stats['by_month']->count() > 0)
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-line text-success me-2"></i>Monthly Expenses Trend
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
                    <div class="progress-bar bg-danger" 
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

{{-- Expenses Table --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-list text-secondary me-2"></i>Expense Details
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($stats['land_cost'] > 0)
                        <tr class="table-info">
                            <td>—</td>
                            <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d-M-Y') : '—' }}</td>
                            <td><span class="badge bg-warning text-dark">Land Cost</span></td>
                            <td><strong>Initial Land Purchase</strong></td>
                            <td class="text-end"><strong>₨ {{ number_format($stats['land_cost'], 2) }}</strong></td>
                            <td class="text-center">—</td>
                        </tr>
                        @endif
                        
                        @forelse($expenses as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-M-Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($expense->category) }}
                                </span>
                            </td>
                            <td>{{ $expense->description ?? '—' }}</td>
                            <td class="text-end">₨ {{ number_format($expense->amount, 2) }}</td>
                            <td class="text-center">
                                @if($expense->attachment)
                                <a href="{{ asset('storage/' . $expense->attachment) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-paperclip"></i>
                                </a>
                                @else
                                —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No operational expenses found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Operational Expenses Subtotal:</td>
                            <td class="text-end">₨ {{ number_format($stats['total_expenses'], 2) }}</td>
                            <td></td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Land Cost:</td>
                            <td class="text-end">₨ {{ number_format($stats['land_cost'], 2) }}</td>
                            <td></td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td colspan="4" class="text-end">Grand Total:</td>
                            <td class="text-end text-danger">₨ {{ number_format($stats['grand_total'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Category Breakdown Chart --}}
@if($stats['by_category']->count() > 0)
<section class="mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-bar text-info me-2"></i>Category Breakdown
            </h5>
            <div class="row">
                @foreach($stats['by_category'] as $category => $amount)
                <div class="col-md-4 mb-3">
                    <div class="text-center p-3 border rounded">
                        <h5 class="text-primary">{{ ucfirst($category) }}</h5>
                        <h4 class="text-danger mb-0">₨ {{ number_format($amount, 2) }}</h4>
                        @php
                            $percentage = $stats['grand_total'] > 0 ? ($amount / $stats['grand_total']) * 100 : 0;
                        @endphp
                        <small class="text-muted">{{ number_format($percentage, 1) }}% of total</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endsection