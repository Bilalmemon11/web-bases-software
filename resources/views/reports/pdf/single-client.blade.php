<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Client Report - {{ $client->name }}</title>
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
            width: 16.66%;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #17a2b8;
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
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .info-table {
            border: none;
        }
        .info-table td {
            border: none;
            padding: 5px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Client Report</h1>
        <p><strong>{{ $client->name }}</strong></p>
        <p>Project: {{ $project->name }}</p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Client Information --}}
    <div class="section">
        <div class="section-title">Client Information</div>
        <table class="info-table">
            <tr>
                <td width="25%"><strong>Name:</strong></td>
                <td width="25%">{{ $client->name }}</td>
                <td width="25%"><strong>Phone:</strong></td>
                <td width="25%">{{ $client->phone ?? '—' }}</td>
            </tr>
            <tr>
                <td><strong>CNIC:</strong></td>
                <td>{{ $client->cnic ?? '—' }}</td>
                <td><strong>Address:</strong></td>
                <td>{{ $client->address ?? '—' }}</td>
            </tr>
            @if($client->notes)
            <tr>
                <td><strong>Notes:</strong></td>
                <td colspan="3">{{ $client->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Summary Statistics --}}
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $summary['sales_count'] }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $summary['total_units'] }}</div>
                <div class="stat-label">Units Purchased</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($summary['total_sales'], 0) }}</div>
                <div class="stat-label">Gross Amount</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">₨ {{ number_format($summary['total_discount'], 0) }}</div>
                <div class="stat-label">Discount</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: green;">₨ {{ number_format($summary['total_paid'], 0) }}</div>
                <div class="stat-label">Amount Paid</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: red;">₨ {{ number_format($summary['total_pending'], 0) }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>

    {{-- Sales History --}}
    <div class="section">
        <div class="section-title">Sales History</div>
        <table>
            <thead>
                <tr>
                    <th width="10%">Date</th>
                    <th width="15%">Units</th>
                    <th width="13%" class="text-right">Gross</th>
                    <th width="11%" class="text-right">Discount</th>
                    <th width="13%" class="text-right">Net</th>
                    <th width="13%" class="text-right">Paid</th>
                    <th width="13%" class="text-right">Pending</th>
                    <th width="12%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($client->sales as $sale)
                <tr>
                    <td>{{ $sale->sale_date->format('d-M-Y') }}</td>
                    <td>
                        @foreach($sale->units as $unit)
                            <span class="badge badge-secondary">{{ $unit->unit_no }}</span>
                        @endforeach
                    </td>
                    <td class="text-right">₨ {{ number_format($sale->total_amount, 2) }}</td>
                    <td class="text-right" style="color: orange;">₨ {{ number_format($sale->discount, 2) }}</td>
                    <td class="text-right"><strong>₨ {{ number_format($sale->net_amount, 2) }}</strong></td>
                    <td class="text-right" style="color: green;">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                    <td class="text-right" style="color: red;">₨ {{ number_format($sale->pending_amount, 2) }}</td>
                    <td class="text-center">
                        @if($sale->status == 'sold')
                        <span class="badge badge-danger">Sold</span>
                        @elseif($sale->status == 'reserved')
                        <span class="badge badge-warning">Reserved</span>
                        @else
                        <span class="badge badge-secondary">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No sales found</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right">Total:</td>
                    <td class="text-right">₨ {{ number_format($summary['total_sales'], 2) }}</td>
                    <td class="text-right">₨ {{ number_format($summary['total_discount'], 2) }}</td>
                    <td class="text-right">₨ {{ number_format($summary['net_amount'], 2) }}</td>
                    <td class="text-right">₨ {{ number_format($summary['total_paid'], 2) }}</td>
                    <td class="text-right">₨ {{ number_format($summary['total_pending'], 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Payment Progress --}}
    @if($summary['net_amount'] > 0)
    <div class="section">
        <div class="section-title">Payment Summary</div>
        <table>
            <tr>
                <td width="50%"><strong>Net Amount to Collect:</strong></td>
                <td class="text-right">₨ {{ number_format($summary['net_amount'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Amount Collected:</strong></td>
                <td class="text-right" style="color: green;">₨ {{ number_format($summary['total_paid'], 2) }}</td>
            </tr>
            <tr style="background-color: #fff3cd;">
                <td><strong>Outstanding Balance:</strong></td>
                <td class="text-right" style="color: red;"><strong>₨ {{ number_format($summary['total_pending'], 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Collection Rate:</strong></td>
                <td class="text-right">
                    @php
                        $collectionRate = ($summary['total_paid'] / $summary['net_amount']) * 100;
                    @endphp
                    <strong>{{ number_format($collectionRate, 2) }}%</strong>
                </td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Client Report: {{ $client->name }} | Confidential Document</p>
    </div>
</body>
</html>