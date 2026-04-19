<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sale Report #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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
            color: #dc3545;
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
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .info-table td {
            border: none;
            padding: 5px;
        }
        .info-box {
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
        .info-box h4 {
            margin: 0 0 10px 0;
            color: #dc3545;
            font-size: 12px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Sale Invoice/Report</h1>
        <p><strong>Sale ID: #{{ $sale->id }}</strong></p>
        <p>Project: {{ $project->name }}</p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Sale and Client Info --}}
    <div class="section">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; padding-right: 10px;">
                <div class="info-box">
                    <h4>Sale Information</h4>
                    <table class="info-table">
                        <tr>
                            <td width="40%"><strong>Sale ID:</strong></td>
                            <td>#{{ $sale->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Sale Date:</strong></td>
                            <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($sale->status == 'sold')
                                <span class="badge badge-danger">Sold</span>
                                @elseif($sale->status == 'reserved')
                                <span class="badge badge-warning">Reserved</span>
                                @else
                                <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 10px;">
                <div class="info-box">
                    <h4>Client Information</h4>
                    <table class="info-table">
                        <tr>
                            <td width="40%"><strong>Name:</strong></td>
                            <td>{{ $sale->client->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td>{{ $sale->client->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><strong>CNIC:</strong></td>
                            <td>{{ $sale->client->cnic ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="section">
        <div class="section-title">Financial Summary</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">₨ {{ number_format($sale->total_amount, 0) }}</div>
                <div class="stat-label">Gross Amount</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">₨ {{ number_format($sale->discount, 0) }}</div>
                <div class="stat-label">Discount</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: green;">₨ {{ number_format($sale->paid_amount, 0) }}</div>
                <div class="stat-label">Amount Paid</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: red;">₨ {{ number_format($sale->pending_amount, 0) }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>

    {{-- Units Purchased --}}
    <div class="section">
        <div class="section-title">Units Purchased</div>
        <table>
            <thead>
                <tr>
                    <th>Unit No</th>
                    <th>Type</th>
                    <th class="text-center">Size (sq ft)</th>
                    <th class="text-right">Unit Price</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->units as $unit)
                <tr>
                    <td><strong>{{ $unit->unit_no }}</strong></td>
                    <td><span class="badge badge-info">{{ ucfirst($unit->type) }}</span></td>
                    <td class="text-center">{{ $unit->size }}</td>
                    <td class="text-right">₨ {{ number_format($unit->pivot->unit_price, 2) }}</td>
                    <td>{{ $unit->location ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-right">₨ {{ number_format($sale->units->sum('pivot.unit_price'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Payment History --}}
    @if($sale->payments->count() > 0)
    <!-- <div class="section">
        <div class="section-title">Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th class="text-right">Amount</th>
                    <th>Payment Method</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                    <td class="text-right" style="color: green;"><strong>₨ {{ number_format($payment->amount, 2) }}</strong></td>
                    <td><span class="badge badge-secondary">{{ ucfirst($payment->payment_method) }}</span></td>
                    <td>{{ $payment->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right">Total Paid:</td>
                    <td class="text-right" style="color: green;">₨ {{ number_format($sale->payments->sum('amount'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div> -->
    @endif

    {{-- Financial Breakdown --}}
    <div class="section">
        <div class="section-title">Financial Breakdown</div>
        <table>
            <tr>
                <td width="60%"><strong>Gross Sale Amount:</strong></td>
                <td class="text-right">₨ {{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Discount Applied:</strong></td>
                <td class="text-right" style="color: orange;">- ₨ {{ number_format($sale->discount, 2) }}</td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td><strong>Net Amount:</strong></td>
                <td class="text-right"><strong>₨ {{ number_format($sale->net_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Amount Paid:</strong></td>
                <td class="text-right" style="color: green;">₨ {{ number_format($sale->paid_amount, 2) }}</td>
            </tr>
            <tr style="background-color: #fff3cd;">
                <td><strong>Outstanding Balance:</strong></td>
                <td class="text-right" style="color: red;"><strong>₨ {{ number_format($sale->pending_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Payment Progress:</strong></td>
                <td class="text-right">
                    @php
                        $progress = $sale->net_amount > 0 ? ($sale->paid_amount / $sale->net_amount) * 100 : 0;
                    @endphp
                    <strong>{{ number_format($progress, 1) }}%</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Sale Report #{{ $sale->id }} | Confidential Document</p>
    </div>
</body>
</html>