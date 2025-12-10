<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Member Report - {{ $member->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #6f42c1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #6f42c1;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #6f42c1;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f8f9fa;
            padding: 7px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 9px;
        }
        table td {
            padding: 7px;
            border: 1px solid #dee2e6;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #6f42c1;
        }
        .stat-label {
            font-size: 8px;
            color: #666;
            margin-top: 5px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        tfoot tr {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .info-table td {
            border: none;
            padding: 5px;
        }
        .highlight-row {
            background-color: #e7d6ff;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Member Investment Report</h1>
        <p><strong>{{ $member->name }}</strong>
            @if($member->is_manager)
            <span class="badge badge-primary">Manager</span>
            @endif
        </p>
        <p>Current Project: {{ $project->name }}</p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Member Information --}}
    <div class="section">
        <div class="section-title">Personal Information</div>
        <table class="info-table">
            <tr>
                <td width="25%"><strong>Name:</strong></td>
                <td width="25%">{{ $member->name }}</td>
                <td width="25%"><strong>Phone:</strong></td>
                <td width="25%">{{ $member->phone ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $member->email ?? '—' }}</td>
                <td><strong>CNIC:</strong></td>
                <td>{{ $member->cnic ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td colspan="3">{{ $member->address ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>Join Date:</strong></td>
                <td>{{ $member->created_at->format('d M, Y') }}</td>
                <td><strong>Total Projects:</strong></td>
                <td><strong>{{ $summary['projects_count'] }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Investment Summary --}}
    <div class="section">
        <div class="section-title">Investment Summary</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($investment['amount'], 0) }}</div>
                <div class="stat-label">Investment in {{ $project->name }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: green;">{{ $investment['profit_share'] }}%</div>
                <div class="stat-label">Profit Share</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">₨ {{ number_format($summary['expected_share'], 0) }}</div>
                <div class="stat-label">Expected Profit</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($summary['total_investment_all_projects'], 0) }}</div>
                <div class="stat-label">Total Investment</div>
            </div>
        </div>
    </div>

    {{-- Current Project Details --}}
    <div class="section">
        <div class="section-title">{{ $project->name }} - Investment Details</div>
        <table>
            <tr>
                <td width="50%"><strong>Investment Amount:</strong></td>
                <td class="text-right">₨ {{ number_format($investment['amount'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Profit Share Percentage:</strong></td>
                <td class="text-right"><strong>{{ $investment['profit_share'] }}%</strong></td>
            </tr>
            <tr>
                <td><strong>Role in Project:</strong></td>
                <td class="text-right">{{ ucfirst($investment['role']) }}</td>
            </tr>
            <tr>
                <td><strong>Project Total Investment:</strong></td>
                <td class="text-right">₨ {{ number_format($project->total_investment, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Your Investment Share:</strong></td>
                <td class="text-right">
                    @php
                        $invPercentage = $project->total_investment > 0 
                            ? ($investment['amount'] / $project->total_investment) * 100 
                            : 0;
                    @endphp
                    {{ number_format($invPercentage, 2) }}%
                </td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td><strong>Current Project Profit/Loss:</strong></td>
                <td class="text-right" style="color: {{ $project->profit >= 0 ? 'green' : 'red' }};">
                    <strong>₨ {{ number_format($project->profit, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Profit Calculation --}}
    <div class="section">
        <div class="section-title">Profit Calculation ({{ $project->name }})</div>
        <table>
            <tr>
                <td width="50%"><strong>Total Project Profit/Loss:</strong></td>
                <td class="text-right" style="color: {{ $project->profit >= 0 ? 'green' : 'red' }};">
                    ₨ {{ number_format($project->profit, 2) }}
                </td>
            </tr>
            <tr>
                <td><strong>Your Profit Share ({{ $investment['profit_share'] }}%):</strong></td>
                <td class="text-right" style="color: {{ $summary['expected_share'] >= 0 ? 'green' : 'red' }};">
                    <strong>₨ {{ number_format($summary['expected_share'], 2) }}</strong>
                </td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td><strong>Expected Return (Investment + Profit):</strong></td>
                <td class="text-right">
                    <strong>₨ {{ number_format($investment['amount'] + $summary['expected_share'], 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Return on Investment (ROI):</strong></td>
                <td class="text-right">
                    @php
                        $roi = $investment['amount'] > 0 
                            ? ($summary['expected_share'] / $investment['amount']) * 100 
                            : 0;
                    @endphp
                    <strong style="color: {{ $roi >= 0 ? 'green' : 'red' }};">
                        {{ number_format($roi, 2) }}%
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Portfolio (All Projects) --}}
    <div class="section">
        <div class="section-title">Complete Portfolio - All Projects</div>
        <table>
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th class="text-right">Investment</th>
                    <th class="text-right">Profit Share</th>
                    <th>Role</th>
                    <th class="text-right">Project Profit</th>
                    <th class="text-right">Your Share</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allProjects as $proj)
                @php
                    $projProfit = $proj->profit;
                    $memberProfitShare = ($proj->pivot->profit_share / 100) * $projProfit;
                @endphp
                <tr class="{{ $proj->id == $project->id ? 'highlight-row' : '' }}">
                    <td>
                        <strong>{{ $proj->name }}</strong>
                        @if($proj->id == $project->id)
                        <span class="badge badge-primary">Current</span>
                        @endif
                    </td>
                    <td class="text-right">₨ {{ number_format($proj->pivot->investment_amount, 2) }}</td>
                    <td class="text-right"><strong>{{ $proj->pivot->profit_share }}%</strong></td>
                    <td><span class="badge badge-secondary">{{ ucfirst($proj->pivot->role) }}</span></td>
                    <td class="text-right" style="color: {{ $projProfit >= 0 ? 'green' : 'red' }};">
                        ₨ {{ number_format($projProfit, 2) }}
                    </td>
                    <td class="text-right" style="color: {{ $memberProfitShare >= 0 ? 'green' : 'red' }};">
                        <strong>₨ {{ number_format($memberProfitShare, 2) }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right">Total:</td>
                    <td class="text-right">₨ {{ number_format($allProjects->sum('pivot.investment_amount'), 2) }}</td>
                    <td colspan="3"></td>
                    <td class="text-right">
                        @php
                            $totalExpectedProfit = $allProjects->sum(function($p) {
                                return ($p->pivot->profit_share / 100) * $p->profit;
                            });
                        @endphp
                        <strong>₨ {{ number_format($totalExpectedProfit, 2) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Overall Portfolio Summary --}}
    <div class="section">
        <div class="section-title">Overall Portfolio Summary</div>
        <table>
            <tr>
                <td width="50%"><strong>Total Investment Across All Projects:</strong></td>
                <td class="text-right">₨ {{ number_format($summary['total_investment_all_projects'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Expected Profit:</strong></td>
                <td class="text-right" style="color: {{ $totalExpectedProfit >= 0 ? 'green' : 'red' }};">
                    ₨ {{ number_format($totalExpectedProfit, 2) }}
                </td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td><strong>Total Expected Return:</strong></td>
                <td class="text-right">
                    <strong>₨ {{ number_format($summary['total_investment_all_projects'] + $totalExpectedProfit, 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Overall ROI:</strong></td>
                <td class="text-right">
                    @php
                        $overallRoi = $summary['total_investment_all_projects'] > 0 
                            ? ($totalExpectedProfit / $summary['total_investment_all_projects']) * 100 
                            : 0;
                    @endphp
                    <strong style="color: {{ $overallRoi >= 0 ? 'green' : 'red' }};">
                        {{ number_format($overallRoi, 2) }}%
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Member Report: {{ $member->name }} | Confidential Document</p>
    </div>
</body>
</html>