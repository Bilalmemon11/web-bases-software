{{-- resources/views/sales/report.blade.php --}}
{{-- Standalone print-optimised page. No layout extends needed. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Outstanding Report — Sale #{{ $sale->id }}</title>
    <style>
        /* ── Reset & Base ───────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            background: #fff;
        }

        /* ── Page Shell ─────────────────────────────────── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 14mm 12mm 20mm;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ─────────────────────────────────────── */
        .report-header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
        }
        .report-header .company { font-size: 18px; font-weight: bold; }
        .report-header .project { font-size: 13px; color: #333; }
        .report-header .report-title { font-size: 12px; font-weight: bold; margin-top: 2px; }

        /* ── Meta Bar ───────────────────────────────────── */
        .meta-bar {
            display: flex;
            justify-content: flex-end;
            font-size: 9.5px;
            color: #555;
            margin-top: 4px;
            margin-bottom: 8px;
            text-align: right;
            line-height: 1.5;
        }

        /* ── Client Info ────────────────────────────────── */
        .client-info {
            display: grid;
            grid-template-columns: 110px 1fr 110px 1fr;
            gap: 3px 0;
            font-size: 10.5px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 6px;
        }
        .client-info .lbl { color: #555; }
        .client-info .val { font-weight: 500; }

        /* ── Pricing Box ────────────────────────────────── */
        .pricing-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 14px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .pricing-box p { margin: 2px 0; font-size: 10.5px; }
        .pricing-box .big-val { font-size: 15px; font-weight: bold; }

        /* ── Two-column layout for tables ───────────────── */
        .tables-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 10px;
            align-items: start;
        }

        /* ── Generic Table ──────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead th {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
        }
        tbody td {
            border: 1px solid #ddd;
            padding: 3px 6px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #fafafa; }
        tfoot td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* ── Financial Summary Box ──────────────────────── */
        .summary-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 14px;
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 24px;
            font-size: 10.5px;
        }
        .summary-box .row-item { display: flex; justify-content: space-between; }
        .summary-box .row-item .lbl { color: #555; }
        .summary-box .row-item .val { font-weight: bold; }

        /* ── Signature Strip ────────────────────────────── */
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            padding-top: 30px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            text-align: center;
        }
        .signatures .sig-line {
            border-top: 1px solid #333;
            padding-top: 4px;
            margin-bottom: 4px;
        }

        .footer-note {
            text-align: center;
            font-size: 9px;
            color: #888;
            margin-top: 4px;
        }

        /* ── Watermark ──────────────────────────────────── */
        .watermark {
            position: fixed;
            top: 38%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 90px;
            font-weight: bold;
            color: rgba(0,0,0,0.04);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Print ──────────────────────────────────────── */
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .page { padding: 10mm 10mm 14mm; }
        }
    </style>
</head>
<body>

{{-- Print Button (hidden on print) --}}
<div class="no-print" style="text-align:center; padding: 10px; background:#f8f9fa; border-bottom: 1px solid #dee2e6;">
    <button onclick="window.print()" style="background:#28a745;color:#fff;border:none;padding:8px 24px;border-radius:4px;cursor:pointer;font-size:13px;">
        🖨️ Print / Save as PDF
    </button>
    <a href="{{ route('sales.show', [$project->slug, $sale->id]) }}"
       style="margin-left:12px;color:#555;text-decoration:none;font-size:12px;">← Back to Sale</a>
</div>

<div class="watermark">{{$sale->remaining_amount == 0 ? 'PAID' : 'PENDING'}}</div>

<div class="page">

    {{-- ── Report Header ───────────────────────────────────────────── --}}
    <div class="report-header">
        <div class="company">{{ config('app.name', 'Laravel') }}</div>
        <div class="project">{{ $project->name }}</div>
        <div class="report-title">Customer Outstanding Report</div>
    </div>

    {{-- ── Meta Bar ────────────────────────────────────────────────── --}}
    <div class="meta-bar">
        <div>
            <strong>Date Printed:</strong> {{ now()->format('d-M-Y g:i a') }}<br>
            <strong>Printed By:</strong> {{ auth()->user()->name ?? 'Manager' }}<br>
            <strong>Report Criteria:</strong> Detail
        </div>
    </div>

    {{-- ── Client / Plot Info ──────────────────────────────────────── --}}
    <div class="client-info">
        <span class="lbl">Client Name:</span>
        <span class="val">{{ $sale->client->name }}</span>
        <span class="lbl" style="padding-left:16px;">Agent Name:</span>
        <span class="val">{{ $sale->client->agent_name ?? '—' }}</span>

        <span class="lbl">Address:</span>
        <span class="val">{{ $sale->client->address ?? '—' }}</span>
        <span class="lbl" style="padding-left:16px;">Mobile #:</span>
        <span class="val">{{ $sale->client->phone ?? '—' }}</span>

        <span class="lbl">Plot No &amp; Size:</span>
        <span class="val">
            {{ $sale->units->pluck('unit_no')->join(', ') }}
            @if($sale->units->first()?->size) – {{ $sale->units->first()->size }} @endif
        </span>
        <span class="lbl" style="padding-left:16px;"></span>
        <span class="val"></span>

        <span class="lbl">Plot Type:</span>
        <span class="val">{{ ucfirst($sale->units->first()?->type ?? '—') }}</span>
        <span class="lbl" style="padding-left:16px;">Mobile #:</span>
        <span class="val">{{ $sale->client->phone ?? '—' }}</span>
    </div>

    {{-- ── Pricing Box ─────────────────────────────────────────────── --}}
    <div class="pricing-box">
        <p>Purchase Price: <span class="big-val">{{ number_format($sale->total_amount, 2) }}</span></p>
        @php
            $nextDue = $sale->remaining_amount;
        @endphp
        <p>Next Due Payment: <span class="big-val">{{ number_format($nextDue, 2) }}</span></p>
    </div>

    {{-- ── Two Table Columns ───────────────────────────────────────── --}}
    <div class="tables-row">
        {{-- Payments Received (right) --}}
        <div>
            <p style="font-weight:bold; margin-bottom:4px;">Payments Information</p>
            <table>
                <thead>
                    <tr>
                        <th>Pay. Date</th>
                        <th>Description</th>
                        <th>Cheque No</th>
                        <th class="text-right">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sale->payments as $pmt)
                    <tr>
                        <td>{{ $pmt->payment_date->format('d/m/Y') }}</td>
                        <td>{{ $pmt->notes ?? ucfirst($pmt->method_label) }}</td>
                        <td>
                            @if($pmt->method === 'cheque')
                                {{ $pmt->cheque_no }} {{ $pmt->bank_name }}
                            @elseif(in_array($pmt->method, ['bank_transfer','online']))
                                {{ $pmt->transaction_ref ?? $pmt->bank_name ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($pmt->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center" style="color:#888;">No payments yet</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">Total Received:</td>
                        <td class="text-right">{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    {{-- ── Financial Summary Box ───────────────────────────────────── --}}
    <div class="summary-box">
        <div class="row-item">
            <span class="lbl">Gross Amount:</span>
            <span class="val">₨{{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="row-item">
            <span class="lbl">Total Paid:</span>
            <span class="val">₨{{ number_format($sale->paid_amount, 2) }}</span>
        </div>

        <div class="row-item">
            <span class="lbl">Discount:</span>
            <span class="val">₨{{ number_format($sale->discount, 2) }}</span>
        </div>
        <div class="row-item">
            <span class="lbl">Outstanding Balance:</span>
            <span class="val">₨{{ number_format($sale->remaining_amount, 2) }}</span>
        </div>

        <div class="row-item">
            <span class="lbl">Net Amount:</span>
            <span class="val">₨{{ number_format($sale->net_amount, 2) }}</span>
        </div>
        <div class="row-item">
            <span class="lbl">Payment Progress:</span>
            <span class="val">{{ $sale->payment_progress }}%</span>
        </div>
    </div>

    {{-- ── Signatures ───────────────────────────────────────────────── --}}
    <div class="signatures">
        <div>
            <div class="sig-line"></div>
            Client Signature
        </div>
        <div>
            <div class="sig-line"></div>
            Agent Signature
        </div>
        <div>
            <div class="sig-line"></div>
            Authorized Signature
        </div>
    </div>

    <div class="footer-note">
        This is a computer-generated report | Sale ID: #{{ $sale->id }}
    </div>

</div>

</body>
</html>