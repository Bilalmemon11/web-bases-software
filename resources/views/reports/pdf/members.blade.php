<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Members Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #28a745;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #28a745;
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
            background-color: #28a745;
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
            padding: 8px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 10px;
        }
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 10px;
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
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            font-size: 10px;
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
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Members Report</h1>
        <p><strong>{{ $project->name }}</strong></p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Summary Statistics --}}
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $members->count() }}</div>
                <div class="stat-label">Total Members</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($members->sum('pivot.investment_amount'), 0) }}</div>
                <div class="stat-label">Total Investment</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $members->sum('pivot.profit_share') }}%</div>
                <div class="stat-label">Total Profit Share</div>
            </div>
        </div>
    </div>

    {{-- Member Details --}}
    <div class="section">
        <div class="section-title">Member Details</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">Sr.</th>
                    <th width="20%">Name</th>
                    <th width="12%">Role</th>
                    <th width="15%" class="text-right">Investment</th>
                    <th width="10%" class="text-right">Profit Share</th>
                    <th width="15%">Contact</th>
                    <th width="13%">CNIC</th>
                    <th width="10%">Manager</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $member->name }}</strong></td>
                    <td>{{ ucfirst($member->pivot->role ?? 'Member') }}</td>
                    <td class="text-right">₨ {{ number_format($member->pivot->investment_amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $member->pivot->profit_share ?? 0 }}%</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td>{{ $member->cnic ?? '—' }}</td>
                    <td class="text-center">{{ $member->is_manager ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">₨ {{ number_format($members->sum('pivot.investment_amount'), 2) }}</td>
                    <td class="text-right">{{ $members->sum('pivot.profit_share') }}%</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Investment Distribution --}}
    <div class="section">
        <div class="section-title">Investment Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th class="text-right">Investment Amount</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalInvestment = $members->sum('pivot.investment_amount');
                @endphp
                @foreach($members as $member)
                @php
                    $percentage = $totalInvestment > 0 ? ($member->pivot->investment_amount / $totalInvestment) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $member->name }}</td>
                    <td class="text-right">₨ {{ number_format($member->pivot->investment_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($percentage, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Additional Notes --}}
    @if($members->where('notes', '!=', null)->count() > 0)
    <div class="section">
        <div class="section-title">Member Notes</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">Member Name</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members->where('notes', '!=', null) as $member)
                <tr>
                    <td><strong>{{ $member->name }}</strong></td>
                    <td>{{ $member->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Members Report | Confidential Document</p>
    </div>
</body>
</html>