@extends('layouts.app')
@section('title', 'Member Report - ' . $member->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-user-tie me-2"></i>Member Report: {{ $member->name }}</h3>
    <div>
        <a href="{{ route('members.index', session('active_project_slug')) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Members
        </a>
        <a href="{{ route('reports.member.download', ['project' => $project->slug, 'member' => $member->id]) }}" 
           class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Download PDF
        </a>
    </div>
</div>

{{-- Member Information --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-id-card text-primary me-2"></i>Personal Information
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Name:</strong></td>
                            <td>
                                {{ $member->name }}
                                @if($member->is_manager)
                                <span class="badge bg-primary ms-2">Manager</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $member->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $member->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>CNIC:</strong></td>
                            <td>{{ $member->cnic ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Address:</strong></td>
                            <td>{{ $member->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Role in Project:</strong></td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($investment['role'] ?? 'Member') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Join Date:</strong></td>
                            <td>{{ $member->created_at->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Projects:</strong></td>
                            <td><strong>{{ $summary['projects_count'] }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Investment Summary --}}
<section class="mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($investment['amount'], 0) }}</h4>
                    <small class="text-muted">Investment in {{ $project->name }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x text-success mb-2"></i>
                    <h4 class="mb-1">{{ $investment['profit_share'] }}%</h4>
                    <small class="text-muted">Profit Share</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($summary['expected_share'], 0) }}</h4>
                    <small class="text-muted">Expected Profit Share</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-briefcase fa-2x text-info mb-2"></i>
                    <h4 class="mb-1">₨ {{ number_format($summary['total_investment_all_projects'], 0) }}</h4>
                    <small class="text-muted">Total Investment (All Projects)</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Current Project Details --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-building text-primary me-2"></i>{{ $project->name }} - Investment Details
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <td width="50%"><strong>Investment Amount:</strong></td>
                            <td class="text-end">₨ {{ number_format($investment['amount'], 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Profit Share:</strong></td>
                            <td class="text-end"><strong>{{ $investment['profit_share'] }}%</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Role:</strong></td>
                            <td class="text-end">{{ ucfirst($investment['role']) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <td width="50%"><strong>Project Total Investment:</strong></td>
                            <td class="text-end">₨ {{ number_format($project->total_investment, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Investment Percentage:</strong></td>
                            <td class="text-end">
                                @php
                                    $invPercentage = $project->total_investment > 0 
                                        ? ($investment['amount'] / $project->total_investment) * 100 
                                        : 0;
                                @endphp
                                {{ number_format($invPercentage, 2) }}%
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Current Project Profit:</strong></td>
                            <td class="text-end text-{{ $project->profit >= 0 ? 'success' : 'danger' }}">
                                ₨ {{ number_format($project->profit, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Profit Calculation --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-calculator text-success me-2"></i>Profit Calculation ({{ $project->name }})
            </h5>
            <table class="table">
                <tr>
                    <td width="50%"><strong>Total Project Profit/Loss:</strong></td>
                    <td class="text-end text-{{ $project->profit >= 0 ? 'success' : 'danger' }}">
                        ₨ {{ number_format($project->profit, 2) }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Your Profit Share ({{ $investment['profit_share'] }}%):</strong></td>
                    <td class="text-end text-{{ $summary['expected_share'] >= 0 ? 'success' : 'danger' }}">
                        <strong>₨ {{ number_format($summary['expected_share'], 2) }}</strong>
                    </td>
                </tr>
                <tr class="table-light">
                    <td><strong>Expected Return:</strong></td>
                    <td class="text-end">
                        <strong>₨ {{ number_format($investment['amount'] + $summary['expected_share'], 2) }}</strong>
                    </td>
                </tr>
                <tr>
                    <td><strong>ROI:</strong></td>
                    <td class="text-end">
                        @php
                            $roi = $investment['amount'] > 0 
                                ? ($summary['expected_share'] / $investment['amount']) * 100 
                                : 0;
                        @endphp
                        <strong class="text-{{ $roi >= 0 ? 'success' : 'danger' }}">
                            {{ number_format($roi, 2) }}%
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</section>

{{-- All Projects Portfolio --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-chart-pie text-info me-2"></i>Portfolio (All Projects)
            </h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th class="text-end">Investment</th>
                            <th class="text-end">Profit Share</th>
                            <th>Role</th>
                            <th class="text-end">Project Profit</th>
                            <th class="text-end">Expected Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allProjects as $proj)
                        @php
                            $projProfit = $proj->profit;
                            $memberProfitShare = ($proj->pivot->profit_share / 100) * $projProfit;
                        @endphp
                        <tr class="{{ $proj->id == $project->id ? 'table-primary' : '' }}">
                            <td>
                                <strong>{{ $proj->name }}</strong>
                                @if($proj->id == $project->id)
                                <span class="badge bg-primary ms-2">Current</span>
                                @endif
                            </td>
                            <td class="text-end">₨ {{ number_format($proj->pivot->investment_amount, 2) }}</td>
                            <td class="text-end"><strong>{{ $proj->pivot->profit_share }}%</strong></td>
                            <td><span class="badge bg-secondary">{{ ucfirst($proj->pivot->role) }}</span></td>
                            <td class="text-end text-{{ $projProfit >= 0 ? 'success' : 'danger' }}">
                                ₨ {{ number_format($projProfit, 2) }}
                            </td>
                            <td class="text-end text-{{ $memberProfitShare >= 0 ? 'success' : 'danger' }}">
                                <strong>₨ {{ number_format($memberProfitShare, 2) }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td class="text-end">Total:</td>
                            <td class="text-end">₨ {{ number_format($allProjects->sum('pivot.investment_amount'), 2) }}</td>
                            <td colspan="3"></td>
                            <td class="text-end">
                                @php
                                    $totalExpectedProfit = $allProjects->sum(function($p) {
                                        return ($p->pivot->profit_share / 100) * $p->profit;
                                    });
                                @endphp
                                ₨ {{ number_format($totalExpectedProfit, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection