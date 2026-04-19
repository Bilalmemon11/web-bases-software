<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Outstanding Report - Sale #{{ $sale->id }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 3px 0 0 0;
            font-size: 14px;
            font-weight: normal;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Client Info Section */
        .client-info {
            margin-bottom: 10px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }
        .info-left {
            display: table-cell;
            width: 60%;
            padding-right: 10px;
        }
        .info-right {
            display: table-cell;
            width: 40%;
            text-align: right;
        }
        .info-label {
            display: inline-block;
            width: 110px;
            font-weight: normal;
        }
        .info-value {
            display: inline-block;
        }
        
        /* Purchase Summary Box */
        .purchase-box {
            background-color: #f5f5f5;
            border: 1px solid #999;
            padding: 10px;
            margin: 10px 0;
        }
        .purchase-row {
            margin: 3px 0;
            font-size: 10px;
        }
        .purchase-label {
            display: inline-block;
            width: 140px;
        }
        .purchase-value {
            font-weight: bold;
            font-size: 12px;
        }
        .next-due {
            /* color: #c00; */
            font-weight: bold;
        }
        
        /* Two-column table layout */
        .tables-section {
            margin-top: 10px;
        }
        .table-container {
            display: table;
            width: 100%;
        }
        .table-half {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        .table-half.left {
            padding-right: 2%;
        }
        
        .section-header {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
            padding-bottom: 2px;
            border-bottom: 1px solid #000;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px;
        }
        table.data-table th {
            background-color: #e8e8e8;
            border: 1px solid #666;
            padding: 4px 3px;
            text-align: left;
            font-weight: bold;
            font-size: 8px;
        }
        table.data-table td {
            border: 1px solid #666;
            padding: 3px;
            font-size: 8px;
        }
        table.data-table td.amount {
            text-align: right;
            font-family: monospace;
            font-weight: bold;
        }
        table.data-table td.center {
            text-align: center;
        }
        .total-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        /* Financial Summary */
        .financial-summary {
            margin-top: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #999;
            font-size: 9px;
        }
        .summary-table {
            width: 100%;
        }
        .summary-table td {
            padding: 4px 5px;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            font-weight: bold;
        }
        .paid-amount {
            /* color: green; */
        }
        .outstanding-amount {
            /* color: red; */
            font-size: 11px;
        }
        .discount-amount {
            /* color: orange; */
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            margin-right: 3%;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 35px;
            padding-top: 5px;
            font-size: 9px;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(200, 200, 200, 0.25);
            z-index: -1;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 5mm;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 7px;
            color: #666;
            padding-right: 10mm;
        }
        
        /* Date and print info in top right */
        .print-info {
            text-align: right;
            font-size: 7px;
            margin-top: -3px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    {{-- Watermark --}}
    @if($sale->pending_amount > 0)
    <div class="watermark">OUTSTANDING</div>
    @else
    <div class="watermark">PAID</div>
    @endif

    {{-- Header --}}
    <div class="header">
        <h1>{{ config('app.name', 'G.M.Sons Builders & Developers') }}</h1>
        <h2>{{ $project->name ?? $project->location ?? 'Diamond Society' }}</h2>
        <p>Customer Outstanding Report</p>
    </div>
    
    {{-- Print Information --}}
    <div class="print-info">
        <strong>Date Printed:</strong> {{ now()->format('d-M-Y') }} {{ now()->format('g:i a') }}<br>
        <strong>Printed By:</strong> {{ auth()->user()->name ?? 'Manager' }}<br>
        <strong>Report Criteria:</strong> Detail
    </div>

    {{-- Client Information --}}
    <div class="client-info">
        <div class="info-row">
            <div class="info-left">
                <span class="info-label">Client Name:</span>
                <span class="info-value">{{ $sale->client->name }}</span>
            </div>
            <div class="info-right">
                <span class="info-label">Agent Name:</span>
                <span class="info-value">{{ $sale->agent_name ?? '—' }}</span>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-left">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $sale->client->address ?? '—' }}</span>
            </div>
            <div class="info-right">
                <span class="info-label">Mobile #:</span>
                <span class="info-value">{{ $sale->client->phone ?? '—' }}</span>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-left">
                <span class="info-label">Plot No & Size:</span>
                <span class="info-value">{{ $sale->units->pluck('unit_no')->join(', ') }} - {{ $sale->units->first()->size ?? '—' }} sq ft</span>
            </div>
            <div class="info-right"></div>
        </div>
        
        <div class="info-row">
            <div class="info-left">
                <span class="info-label">Plot Type:</span>
                <span class="info-value">{{ $sale->units->first()->type ?? '—' }}</span>
            </div>
            <div class="info-right">
                <span class="info-label">Mobile #:</span>
                <span class="info-value">{{ $sale->client->phone ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Purchase Summary Box --}}
    <div class="purchase-box">
        <div class="purchase-row">
            <span class="purchase-label">Purchase Price:</span>
            <span class="purchase-value">{{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="purchase-row">
            <span class="purchase-label">Next Due Payment:</span>
            <span class="purchase-value next-due">
                @php
                    $nextDue = $sale->installments()
                        ->where('status', '!=', 'paid')
                        ->orderBy('due_date')
                        ->first();
                @endphp
                {{ $nextDue ? number_format($nextDue->remaining_amount, 2) : '0.00' }}
            </span>
        </div>
    </div>

    {{-- Two-Column Tables Section --}}
    <div class="tables-section">
        <div class="table-container">
            {{-- Left Column: Due Payments --}}
            <div class="table-half left">
                <div class="section-header">Due Payments</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Due Date</th>
                            <th style="width: 55%;">Description</th>
                            <th style="width: 25%; text-align: right;">Amount Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($sale->has_installments && $sale->installments->count() > 0)
                            @foreach($sale->installments as $installment)
                            <tr>
                                <td>{{ $installment->due_date->format('d/m/Y') }}</td>
                                <td>{{ $installment->description }}</td>
                                <td class="amount">{{ number_format($installment->amount_due, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td>Full Payment</td>
                                <td class="amount">{{ number_format($sale->net_amount, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Right Column: Payments Information --}}
            <div class="table-half">
                <div class="section-header">Payments Information</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 18%;">Pay. Date</th>
                            <th style="width: 37%;">Description</th>
                            <th style="width: 20%;">Cheque No</th>
                            <th style="width: 25%; text-align: right;">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($sale->payments->count() > 0)
                            @foreach($sale->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>{{ $payment->description ?? '—' }}</td>
                                <td class="center">
                                    @if($payment->payment_method == 'cheque')
                                        {{ $payment->cheque_no }} {{ $payment->bank }}
                                    @elseif($payment->payment_method == 'bank_transfer')
                                        {{ $payment->bank }}
                                    @else
                                        {{ strtoupper($payment->payment_method) }}
                                    @endif
                                </td>
                                <td class="amount">{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Total Received:</td>
                                <td class="amount">{{ number_format($sale->paid_amount, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 15px; color: #999;">
                                    No payments recorded yet
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Financial Summary at Bottom --}}
    <div class="financial-summary">
        <table class="summary-table">
            <tr>
                <td style="width: 50%;">
                    <span class="summary-label">Gross Amount:</span> ₨{{ number_format($sale->total_amount, 2) }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <span class="summary-label">Total Paid:</span> <span class="paid-amount">₨{{ number_format($sale->paid_amount, 2) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="summary-label">Discount:</span> <span class="discount-amount">₨{{ number_format($sale->discount, 2) }}</span>
                </td>
                <td style="text-align: right;">
                    <span class="summary-label">Outstanding Balance:</span> <span class="outstanding-amount">₨{{ number_format($sale->pending_amount, 2) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="summary-label">Net Amount:</span> ₨{{ number_format($sale->net_amount, 2) }}
                </td>
                <td style="text-align: right;">
                    @php
                        $progress = $sale->net_amount > 0 ? ($sale->paid_amount / $sale->net_amount) * 100 : 0;
                    @endphp
                    <span class="summary-label">Payment Progress:</span> {{ number_format($progress, 1) }}%
                </td>
            </tr>
        </table>
    </div>

    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Client Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Agent Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Authorized Signature</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer" style="margin-top: 5px;">
        This is a computer-generated report | Sale ID: #{{ $sale->id }}
    </div>
</body>
</html>