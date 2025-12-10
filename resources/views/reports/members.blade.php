@extends('layouts.app')
@section('title', 'Members Report - Junaid Builders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-users me-2"></i>Members Report</h3>
    <div>
        <a href="{{ route('reports.index', $project->slug) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
        <a href="{{ route('reports.download', ['project' => $project->slug, 'type' => 'members']) }}" 
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
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h3 class="mb-1">{{ $members->count() }}</h3>
                    <small class="text-muted">Total Members</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x text-success mb-2"></i>
                    <h3 class="mb-1">₨ {{ number_format($totalInvestment, 2) }}</h3>
                    <small class="text-muted">Total Investment</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-2x text-warning mb-2"></i>
                    <h3 class="mb-1">{{ $members->sum('pivot.profit_share') }}%</h3>
                    <small class="text-muted">Total Profit Share</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Members Table --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-list text-secondary me-2"></i>Member Details
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th class="text-end">Investment Amount</th>
                            <th class="text-end">Profit Share</th>
                            <th>Contact</th>
                            <th>CNIC</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $member->name }}</strong>
                                @if($member->is_manager)
                                <span class="badge bg-primary ms-1">Manager</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($member->pivot->role ?? 'Member') }}
                                </span>
                            </td>
                            <td class="text-end">₨ {{ number_format($member->pivot->investment_amount ?? 0, 2) }}</td>
                            <td class="text-end">
                                <strong>{{ $member->pivot->profit_share ?? 0 }}%</strong>
                            </td>
                            <td>{{ $member->phone ?? '—' }}</td>
                            <td>{{ $member->cnic ?? '—' }}</td>
                            <td>{{ $member->address ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No members found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-end">₨ {{ number_format($totalInvestment, 2) }}</td>
                            <td class="text-end">{{ $members->sum('pivot.profit_share') }}%</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Investment Breakdown Chart --}}
@if($members->count() > 0)
<section class="mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-bar text-primary me-2"></i>Investment Distribution
            </h5>
            <div class="row">
                @foreach($members as $member)
                @php
                    $percentage = $totalInvestment > 0 ? ($member->pivot->investment_amount / $totalInvestment) * 100 : 0;
                @endphp
                <div class="col-md-6 mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>{{ $member->name }}</small>
                        <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $percentage }}%"
                             aria-valuenow="{{ $percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            ₨ {{ number_format($member->pivot->investment_amount, 0) }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endsection