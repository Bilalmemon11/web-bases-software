<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #dc3545;
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
            background-color: #dc3545;
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
            padding: 6px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 8px;
        }
        table td {
            padding: 6px;
            border: 1px solid #dee2e6;
            font-size: 8px;
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
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #dc3545;
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
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
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
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Sales Report</h1>
        <p><strong>{{ $project->name }}</strong></p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Summary Statistics --}}
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        @php
            $totalSales = $sales->count();
            $totalAmount = $sales->sum('total_amount');
            $totalDiscount = $sales->sum('discount');
            $totalReceived = $sales->sum('paid_amount');
            $totalPending = $sales->sum('pending_amount');
        @endphp
        <div class="stats-grid">
            <div class="stat-box" style="width: 16.66%;">
                <div class="stat-value">{{ $totalSales }}</div>
                <div class="stat-label">Total Sales</div>
            </div>
            <div class="stat-box" style="width: 16.66%;">
                <div class="stat-value">₨ {{ number_format($totalAmount, 0) }}</div>
                <div class="stat-label">Gross Amount</div>
            </div>
            <div class="stat-box" style="width: 16.66%;">
                <div class="stat-value" style="color: orange;">₨ {{ number_format($totalDiscount, 0) }}</div>
                <div class="stat-label">Discounts</div>
            </div>
            <div class="stat-box" style="width: 16.66%;">
                <div class="stat-value">₨ {{ number_format($totalAmount - $totalDiscount, 0) }}</div>
                <div class="stat-label">Net Amount</div>
            </div>
            <div class="stat-box" style="width: 16.66%;">
                <div class="stat-value" style="color: green;">₨ {{ number_format($totalReceived, 0) }}</div>
                <div class="stat-label">Received</div>
            </div>
            <div class="stat-box" style="width: 16.66%;">
                <div class="stat-value" style="color: red;">₨ {{ number_format($totalPending, 0) }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>

    {{-- Sales by Status --}}
    <div class="section">
        <div class="section-title">Sales by Status</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Count</th>
                    <th class="text-right">Percentage</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales->groupBy('status') as $status => $statusSales)
                @php
                    $percentage = $totalSales > 0 ? ($statusSales->count() / $totalSales) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ ucfirst($status) }}</strong></td>
                    <td class="text-center">{{ $statusSales->count() }}</td>
                    <td class="text-right">{{ number_format($percentage, 2) }}%</td>
                    <td class="text-right">₨ {{ number_format($statusSales->sum('total_amount'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Sales Details --}}
    <div class="section">
        <div class="section-title">Sales Transaction Details</div>
        <table>
            <thead>
                <tr>
                    <th width="4%">Sr.</th>
                    <th width="9%">Date</th>
                    <th width="13%">Client</th>
                    <th width="11%">Units</th>
                    <th width="11%" class="text-right">Total</th>
                    <th width="9%" class="text-right">Disc.</th>
                    <th width="11%" class="text-right">Net</th>
                    <th width="11%" class="text-right">Paid</th>
                    <th width="11%" class="text-right">Pending</th>
                    <th width="10%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $index => $sale)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sale->sale_date->format('d-M-Y') }}</td>
                    <td><strong>{{ $sale->client->name }}</strong></td>
                    <td>
                        @foreach($sale->units as $unit)
                            <span class="badge badge-secondary">{{ $unit->unit_no }}</span>
                        @endforeach
                    </td>
                    <td class="text-right">₨ {{ number_format($sale->total_amount, 2) }}</td>
                    <td class="text-right" style="color: red;">₨ {{ number_format($sale->discount, 2) }}</td>
                    <td class="text-right"><strong>₨ {{ number_format($sale->net_amount, 2) }}</strong></td>
                    <td class="text-right" style="color: green;">₨ {{ number_format($sale->paid_amount, 2) }}</td>
                    <td class="text-right" style="color: orange;">₨ {{ number_format($sale->pending_amount, 2) }}</td>
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
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Total:</td>
                    <td class="text-right">₨ {{ number_format($totalAmount, 2) }}</td>
                    <td class="text-right">₨ {{ number_format($totalDiscount, 2) }}</td>
                    <td class="text-right">₨ {{ number_format($totalAmount - $totalDiscount, 2) }}</td>
                    <td class="text-right">₨ {{ number_format($totalReceived, 2) }}</td>
                    <td class="text-right">₨ {{ number_format($totalPending, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Monthly Sales Trend --}}
    @php
        $monthlySales = $sales->groupBy(fn($s) => $s->sale_date->format('M Y'));
    @endphp
    @if($monthlySales->count() > 0)
    <div class="section">
        <div class="section-title">Monthly Sales Trend</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center">Number of Sales</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Amount Received</th>
                    <th class="text-right">Pending Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlySales as $month => $monthSales)
                <tr>
                    <td><strong>{{ $month }}</strong></td>
                    <td class="text-center">{{ $monthSales->count() }}</td>
                    <td class="text-right">₨ {{ number_format($monthSales->sum('total_amount'), 2) }}</td>
                    <td class="text-right">₨ {{ number_format($monthSales->sum('paid_amount'), 2) }}</td>
                    <td class="text-right">₨ {{ number_format($monthSales->sum('pending_amount'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Payment Collection Status --}}
    <div class="section">
        <div class="section-title">Payment Collection Summary</div>
        <table>
            <tr>
                <td width="50%"><strong>Total Billed Amount:</strong></td>
                <td class="text-right">₨ {{ number_format($totalAmount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Discounts Given:</strong></td>
                <td class="text-right" style="color: red;">₨ {{ number_format($totalDiscount, 2) }}</td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td><strong>Net Amount to Collect:</strong></td>
                <td class="text-right"><strong>₨ {{ number_format($totalAmount - $totalDiscount, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Amount Collected:</strong></td>
                <td class="text-right" style="color: green;">₨ {{ number_format($totalReceived, 2) }}</td>
            </tr>
            <tr style="background-color: #fff3cd;">
                <td><strong>Outstanding Balance:</strong></td>
                <td class="text-right" style="color: red;"><strong>₨ {{ number_format($totalPending, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Collection Rate:</strong></td>
                <td class="text-right">
                    @php
                        $netAmount = $totalAmount - $totalDiscount;
                        $collectionRate = $netAmount > 0 ? ($totalReceived / $netAmount) * 100 : 0;
                    @endphp
                    <strong>{{ number_format($collectionRate, 2) }}%</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Sales Report | Confidential Document</p>
    </div>
</body>
</html>