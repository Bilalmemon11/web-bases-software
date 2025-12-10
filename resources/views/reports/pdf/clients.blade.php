<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clients Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #17a2b8;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #17a2b8;
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
            background-color: #17a2b8;
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
            font-size: 18px;
            font-weight: bold;
            color: #17a2b8;
        }
        .stat-label {
            font-size: 9px;
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
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Clients Report</h1>
        <p><strong>{{ $project->name }}</strong></p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Summary Statistics --}}
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        @php
            $totalSales = 0;
            $totalPaid = 0;
            $totalPending = 0;
            $totalUnits = 0;
            
            foreach($clients as $client) {
                $sales = $client->sales()->where('project_id', $project->id)->get();
                $totalSales += $sales->sum('total_amount');
                $totalPaid += $sales->sum('paid_amount');
                $totalPending += $sales->sum('pending_amount');
                $totalUnits += $sales->flatMap->units->count();
            }
        @endphp
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $clients->count() }}</div>
                <div class="stat-label">Total Clients</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($totalSales, 0) }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: green;">₨ {{ number_format($totalPaid, 0) }}</div>
                <div class="stat-label">Total Paid</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">₨ {{ number_format($totalPending, 0) }}</div>
                <div class="stat-label">Total Pending</div>
            </div>
        </div>
    </div>

    {{-- Client Details --}}
    <div class="section">
        <div class="section-title">Client Details</div>
        <table>
            <thead>
                <tr>
                    <th width="4%">Sr.</th>
                    <th width="15%">Name</th>
                    <th width="12%">Contact</th>
                    <th width="12%">CNIC</th>
                    <th width="8%" class="text-center">Units</th>
                    <th width="13%" class="text-right">Total Sales</th>
                    <th width="12%" class="text-right">Paid</th>
                    <th width="12%" class="text-right">Pending</th>
                    <th width="12%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $index => $client)
                @php
                    $sales = $client->sales()->where('project_id', $project->id)->get();
                    $clientTotal = $sales->sum('total_amount');
                    $clientPaid = $sales->sum('paid_amount');
                    $clientPending = $sales->sum('pending_amount');
                    $clientUnits = $sales->flatMap->units->count();
                    $paymentProgress = $clientTotal > 0 ? ($clientPaid / $clientTotal) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $client->name }}</strong></td>
                    <td>{{ $client->phone ?? '—' }}</td>
                    <td>{{ $client->cnic ?? '—' }}</td>
                    <td class="text-center">{{ $clientUnits }}</td>
                    <td class="text-right">₨ {{ number_format($clientTotal, 2) }}</td>
                    <td class="text-right" style="color: green;">₨ {{ number_format($clientPaid, 2) }}</td>
                    <td class="text-right" style="color: red;">₨ {{ number_format($clientPending, 2) }}</td>
                    <td class="text-center">
                        @if($paymentProgress >= 100)
                        <span class="badge badge-success">Paid</span>
                        @elseif($paymentProgress > 0)
                        <span class="badge badge-warning">Partial</span>
                        @else
                        <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Total:</td>
                    <td class="text-center">{{ $totalUnits }}</td>
                    <td class="text-right">₨ {{ number_format($totalSales, 2) }}</td>
                    <td class="text-right">₨ {{ number_format($totalPaid, 2) }}</td>
                    <td class="text-right">₨ {{ number_format($totalPending, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Detailed Client Breakdown --}}
    <div class="section">
        <div class="section-title">Detailed Client Information</div>
        @foreach($clients as $client)
        @php
            $sales = $client->sales()->where('project_id', $project->id)->with('units')->get();
        @endphp
        <div style="margin-bottom: 20px; border: 1px solid #dee2e6; padding: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #17a2b8;">{{ $client->name }}</h4>
            <table style="margin-bottom: 10px;">
                <tr>
                    <td width="25%"><strong>Contact:</strong></td>
                    <td width="25%">{{ $client->phone ?? '—' }}</td>
                    <td width="25%"><strong>CNIC:</strong></td>
                    <td width="25%">{{ $client->cnic ?? '—' }}</td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td colspan="3">{{ $client->address ?? '—' }}</td>
                </tr>
            </table>
            
            <table>
                <thead>
                    <tr>
                        <th>Sale Date</th>
                        <th>Units Purchased</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Paid</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('d-M-Y') }}</td>
                        <td>
                            @foreach($sale->units as $unit)
                                {{ $unit->unit_no }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                        <td class="text-right">₨ {{ number_format($sale->total_amount, 2) }}</td>
                        <td class="text-right">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                        <td class="text-right">₨ {{ number_format($sale->discount, 2) }}</td>
                        <td class="text-right">₨ {{ number_format($sale->pending_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Clients Report | Confidential Document</p>
    </div>
</body>
</html>