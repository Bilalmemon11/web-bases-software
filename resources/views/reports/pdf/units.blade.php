<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Units Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #ffc107;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #ffc107;
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
            background-color: #ffc107;
            color: #333;
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
            font-size: 20px;
            font-weight: bold;
            color: #ffc107;
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
        .group-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
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
        <h1>Units Report</h1>
        <p><strong>{{ $project->name }}</strong></p>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    {{-- Summary Statistics --}}
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        @php
            $total = $units->count();
            $available = $units->where('status', 'available')->count();
            $reserved = $units->where('status', 'reserved')->count();
            $sold = $units->where('status', 'sold')->count();
            $totalValue = $units->sum('sale_price');
            $soldValue = $units->where('status', 'sold')->sum('sale_price');
        @endphp
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $total }}</div>
                <div class="stat-label">Total Units</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: green;">{{ $available }}</div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: orange;">{{ $reserved }}</div>
                <div class="stat-label">Reserved</div>
            </div>
            <div class="stat-box">
                <div class="stat-value" style="color: red;">{{ $sold }}</div>
                <div class="stat-label">Sold</div>
            </div>
        </div>

        <table>
            <tr>
                <td width="50%"><strong>Total Inventory Value:</strong></td>
                <td class="text-right">₨ {{ number_format($totalValue, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Sold Units Value:</strong></td>
                <td class="text-right">₨ {{ number_format($soldValue, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Remaining Inventory Value:</strong></td>
                <td class="text-right">₨ {{ number_format($totalValue - $soldValue, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Units by Type --}}
    <div class="section">
        <div class="section-title">Units Distribution by Type</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th class="text-center">Total Units</th>
                    <th class="text-center">Available</th>
                    <th class="text-center">Reserved</th>
                    <th class="text-center">Sold</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units->groupBy('type') as $type => $typeUnits)
                <tr>
                    <td><strong>{{ ucfirst($type) }}</strong></td>
                    <td class="text-center">{{ $typeUnits->count() }}</td>
                    <td class="text-center">{{ $typeUnits->where('status', 'available')->count() }}</td>
                    <td class="text-center">{{ $typeUnits->where('status', 'reserved')->count() }}</td>
                    <td class="text-center">{{ $typeUnits->where('status', 'sold')->count() }}</td>
                    <td class="text-right">₨ {{ number_format($typeUnits->sum('sale_price'), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Complete Unit Listing --}}
    <div class="section">
        <div class="section-title">Complete Unit Listing</div>
        <table>
            <thead>
                <tr>
                    <th width="6%">Sr.</th>
                    <th width="12%">Unit No</th>
                    <th width="15%">Type</th>
                    <th width="12%">Size</th>
                    <th width="15%" class="text-right">Sale Price</th>
                    <th width="10%" class="text-center">Status</th>
                    <th width="18%">Client</th>
                    <th width="12%">Sale Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units->groupBy('type') as $type => $groupedUnits)
                {{-- Group Header --}}
                <tr class="group-header">
                    <td colspan="8">{{ strtoupper($type) }} ({{ $groupedUnits->count() }} units)</td>
                </tr>
                {{-- Units --}}
                @foreach($groupedUnits as $unit)
                <tr>
                    <td>{{ $loop->parent->index + $loop->index + 1 }}</td>
                    <td><strong>{{ $unit->unit_no }}</strong></td>
                    <td>{{ ucfirst($unit->type) }}</td>
                    <td>{{ $unit->size }}</td>
                    <td class="text-right">₨ {{ number_format($unit->sale_price, 2) }}</td>
                    <td class="text-center">
                        @if($unit->status == 'sold')
                        <span class="badge badge-danger">Sold</span>
                        @elseif($unit->status == 'reserved')
                        <span class="badge badge-warning">Reserved</span>
                        @else
                        <span class="badge badge-success">Available</span>
                        @endif
                    </td>
                    <td>{{ $unit->soldTo()?->name ?? $unit->reservedBy()?->name ?? '—' }}</td>
                    <td>{{ $unit->soldSale()?->sale_date?->format('d-M-Y') ?? $unit->reservedSale()?->sale_date?->format('d-M-Y') ?? '—' }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Status Distribution --}}
    <div class="section">
        <div class="section-title">Status Distribution Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-center">Count</th>
                    <th class="text-right">Percentage</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $statusData = [
                        ['name' => 'Available', 'count' => $available, 'color' => 'success'],
                        ['name' => 'Reserved', 'count' => $reserved, 'color' => 'warning'],
                        ['name' => 'Sold', 'count' => $sold, 'color' => 'danger']
                    ];
                @endphp
                @foreach($statusData as $status)
                @php
                    $percentage = $total > 0 ? ($status['count'] / $total) * 100 : 0;
                    $statusUnits = $units->where('status', strtolower($status['name']));
                    $statusValue = $statusUnits->sum('sale_price');
                @endphp
                <tr>
                    <td><strong>{{ $status['name'] }}</strong></td>
                    <td class="text-center">{{ $status['count'] }}</td>
                    <td class="text-right">{{ number_format($percentage, 2) }}%</td>
                    <td class="text-right">₨ {{ number_format($statusValue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $project->name }} - Units Report | Page 1</p>
    </div>
</body>
</html>