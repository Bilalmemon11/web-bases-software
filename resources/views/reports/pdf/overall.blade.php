<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overall Project Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #007bff;
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
            background-color: #007bff;
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
        }
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
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
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
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
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Overall Project Report</h1>
        <p><strong>{{ $project->name }}</strong></p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Project Information --}}
    <div class="section">
        <div class="section-title">Project Information</div>
        <table>
            <tr>
                <td width="30%"><strong>Project Name:</strong></td>
                <td>{{ $project->name }}</td>
                <td width="30%"><strong>Status:</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</td>
            </tr>
            <tr>
                <td><strong>Start Date:</strong></td>
                <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M, Y') : '—' }}</td>
                <td><strong>End Date:</strong></td>
                <td>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M, Y') : '—' }}</td>
            </tr>
            <tr>
                <td><strong>Total Members:</strong></td>
                <td>{{ $members->count() }}</td>
                <td><strong>Total Units:</strong></td>
                <td>{{ $units_count }}</td>
            </tr>
            <tr>
                <td><strong>Total Clients:</strong></td>
                <td>{{ $clients_count }}</td>
                <td><strong>Total Sales:</strong></td>
                <td>{{ $sales_count }}</td>
            </tr>
        </table>
    </div>

    {{-- Financial Summary --}}
    <div class="section">
        <div class="section-title">Financial Summary</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($total_investment, 0) }}</div>
                <div class="stat-label">Total Investment</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($total_expenses, 0) }}</div>
                <div class="stat-label">Total Expenses</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($total_sales, 0) }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: {{ $profit >= 0 ? 'green' : 'red' }}">
                    ₨ {{ number_format($profit, 0) }}
                </div>
                <div class="stat-label">Projected Profit</div>
            </div>
        </div>

        <table>
            <tr>
                <td width="50%"><strong>Amount Received:</strong></td>
                <td class="text-right">₨ {{ number_format($total_received, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Pending Amount:</strong></td>
                <td class="text-right">₨ {{ number_format($total_pending, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Discounts:</strong></td>
                <td class="text-right">₨ {{ number_format($total_discount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Project Progress:</strong></td>
                <td class="text-right">{{ number_format($progress, 1) }}%</td>
            </tr>
        </table>
    </div>

    {{-- Units Overview --}}
    <div class="section">
        <div class="section-title">Units Overview</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $units_count }}</div>
                <div class="stat-label">Total Units</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: green;">{{ $units_available }}</div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">{{ $units_reserved }}</div>
                <div class="stat-label">Reserved</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: red;">{{ $units_sold }}</div>
                <div class="stat-label">Sold</div>
            </div>
        </div>
    </div>

    {{-- Members Summary --}}
    <div class="section">
        <div class="section-title">Project Members</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th class="text-right">Investment</th>
                    <th class="text-right">Profit Share</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ ucfirst($member->pivot->role ?? 'Member') }}</td>
                    <td class="text-right">₨ {{ number_format($member->pivot->investment_amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $member->pivot->profit_share ?? 0 }}%</td>
                    <td>{{ $member->phone }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Overall Project Report | Page 1</p>
    </div>
</body>
</html>