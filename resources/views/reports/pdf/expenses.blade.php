<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expenses Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #6c757d;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #6c757d;
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
            background-color: #6c757d;
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
            width: 33.33%;
            padding: 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #6c757d;
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
            background-color: #6c757d;
            color: white;
        }
        .highlight-row {
            background-color: #e7f3ff;
        }
        .total-row {
            background-color: #fff3cd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Expenses Report</h1>
        <p><strong>{{ $project->name }}</strong></p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Summary Statistics --}}
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        @php
            $totalExpenses = $expenses->sum('amount');
            $landCost = $project->land_cost ?? 0;
            $grandTotal = $totalExpenses + $landCost;
        @endphp
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $expenses->count() }}</div>
                <div class="stat-label">Total Expense Records</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">₨ {{ number_format($landCost, 0) }}</div>
                <div class="stat-label">Land Cost</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: red;">₨ {{ number_format($totalExpenses, 0) }}</div>
                <div class="stat-label">Operational Expenses</div>
            </div>
        </div>

        <table>
            <tr class="total-row">
                <td width="50%"><strong>GRAND TOTAL EXPENSES:</strong></td>
                <td class="text-right" style="color: red; font-size: 14px;">
                    <strong>₨ {{ number_format($grandTotal, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    {{-- Expenses by Category --}}
    @php
        $expensesByCategory = $expenses->groupBy('category');
    @endphp
    @if($expensesByCategory->count() > 0)
    <div class="section">
        <div class="section-title">Expenses by Category</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-center">Number of Expenses</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expensesByCategory as $category => $categoryExpenses)
                @php
                    $categoryTotal = $categoryExpenses->sum('amount');
                    $percentage = $totalExpenses > 0 ? ($categoryTotal / $totalExpenses) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ ucfirst($category) }}</strong></td>
                    <td class="text-center">{{ $categoryExpenses->count() }}</td>
                    <td class="text-right">₨ {{ number_format($categoryTotal, 2) }}</td>
                    <td class="text-right">{{ number_format($percentage, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total Operational:</strong></td>
                    <td class="text-center">{{ $expenses->count() }}</td>
                    <td class="text-right">₨ {{ number_format($totalExpenses, 2) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- Monthly Expenses --}}
    @php
        $monthlyExpenses = $expenses->groupBy(fn($e) => \Carbon\Carbon::parse($e->expense_date)->format('M Y'));
    @endphp
    @if($monthlyExpenses->count() > 0)
    <div class="section">
        <div class="section-title">Monthly Expenses Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-center">Number of Expenses</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlyExpenses as $month => $monthExpenses)
                <tr>
                    <td><strong>{{ $month }}</strong></td>
                    <td class="text-center">{{ $monthExpenses->count() }}</td>
                    <td class="text-right">₨ {{ number_format($monthExpenses->sum('amount'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Complete Expense Listing --}}
    <div class="section">
        <div class="section-title">Complete Expense Listing</div>
        
        {{-- Land Cost Entry --}}
        @if($landCost > 0)
        <table>
            <thead>
                <tr>
                    <th width="10%">Date</th>
                    <th width="18%">Category</th>
                    <th width="50%">Description</th>
                    <th width="22%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr class="highlight-row">
                    <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d-M-Y') : '—' }}</td>
                    <td><span class="badge" style="background-color: #ffc107; color: black;">LAND COST</span></td>
                    <td><strong>Initial Land Purchase</strong></td>
                    <td class="text-right"><strong>₨ {{ number_format($landCost, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Operational Expenses --}}
        <table>
            <thead>
                <tr>
                    <th width="5%">Sr.</th>
                    <th width="10%">Date</th>
                    <th width="15%">Category</th>
                    <th width="48%">Description</th>
                    <th width="22%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $index => $expense)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-M-Y') }}</td>
                    <td><span class="badge">{{ ucfirst($expense->category) }}</span></td>
                    <td>{{ $expense->description ?? '—' }}</td>
                    <td class="text-right">₨ {{ number_format($expense->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #999;">No operational expenses recorded</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right">Operational Expenses Subtotal:</td>
                    <td class="text-right">₨ {{ number_format($totalExpenses, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Land Cost:</td>
                    <td class="text-right">₨ {{ number_format($landCost, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>GRAND TOTAL:</strong></td>
                    <td class="text-right" style="color: red;">
                        <strong>₨ {{ number_format($grandTotal, 2) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Cost Breakdown --}}
    <div class="section">
        <div class="section-title">Cost Structure Analysis</div>
        <table>
            <thead>
                <tr>
                    <th>Cost Type</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Percentage of Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Land Cost</strong></td>
                    <td class="text-right">₨ {{ number_format($landCost, 2) }}</td>
                    <td class="text-right">
                        {{ $grandTotal > 0 ? number_format(($landCost / $grandTotal) * 100, 2) : 0 }}%
                    </td>
                </tr>
                @foreach($expensesByCategory as $category => $categoryExpenses)
                @php
                    $categoryTotal = $categoryExpenses->sum('amount');
                @endphp
                <tr>
                    <td>{{ ucfirst($category) }}</td>
                    <td class="text-right">₨ {{ number_format($categoryTotal, 2) }}</td>
                    <td class="text-right">
                        {{ $grandTotal > 0 ? number_format(($categoryTotal / $grandTotal) * 100, 2) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total Project Cost:</strong></td>
                    <td class="text-right"><strong>₨ {{ number_format($grandTotal, 2) }}</strong></td>
                    <td class="text-right"><strong>100%</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Expenses Report | Confidential Document</p>
    </div>
</body>
</html>