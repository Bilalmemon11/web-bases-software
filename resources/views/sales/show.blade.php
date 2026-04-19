@extends('layouts.app')
@section('title', 'Sale Details')

@section('content')
<section>
    <div class="d-flex justify-content-between mb-3">
        <h4>Sale Details - #{{ $sale->id }}</h4>
        <div>
            <a href="{{ route('sales.payments.create', [$project->slug, $sale->id]) }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> Record Payment
            </a>
            <a href="{{ route('reports.sale.download', ['project' => $project->slug, 'sale' => $sale->id]) }}"
                class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            <a href="{{ route('sales.index', $project->slug) }}" class="btn btn-secondary">Back to Sales</a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Sale Information --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Sale Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Sale ID:</strong> #{{ $sale->id }}</p>
                    <p class="mb-2"><strong>Client:</strong> {{ $sale->client->name }}</p>
                    <p class="mb-2"><strong>Sale Date:</strong> {{ $sale->sale_date->format('d M, Y') }}</p>
                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge {{ $sale->status=='sold'?'bg-success':($sale->status=='reserved'?'bg-warning text-dark':'bg-secondary') }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </p>
                    <p class="mb-2"><strong>Payment Type:</strong> {{ $sale->has_installments ? 'Installments' : 'Direct Payment' }}</p>
                    @if($sale->has_installments)
                    <p class="mb-2"><strong>Installments:</strong> {{ $sale->installment_count }}</p>
                    @endif
                </div>
            </div>

            {{-- Units --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-building text-info me-2"></i>Units</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($sale->units as $unit)
                        <li class="mb-2">
                            <strong>{{ $unit->unit_no }}</strong> - {{ ucfirst($unit->type) }}<br>
                            <small class="text-muted">₨{{ number_format($unit->pivot->unit_price, 2) }}</small>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-calculator text-success me-2"></i>Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Amount:</span>
                        <strong>₨{{ number_format($sale->total_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Discount:</span>
                        <strong>- ₨{{ number_format($sale->discount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Net Total:</strong>
                        <strong>₨{{ number_format($sale->net_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Total Paid:</span>
                        <strong>₨{{ number_format($sale->paid_amount, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Remaining:</strong>
                        <strong class="text-danger">₨{{ number_format($sale->pending_amount, 2) }}</strong>
                    </div>

                    @if($sale->is_fully_paid)
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="fas fa-check-circle me-2"></i>Fully Paid
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Installments & Payments --}}
        <div class="col-lg-8">
            @if($sale->has_installments)
            {{-- Installments Schedule --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Installment Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Paid</th>
                                    <th>Remaining</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->installments as $inst)
                                <tr>
                                    <td>{{ $inst->installment_number }}</td>
                                    <td>{{ $inst->description }}</td>
                                    <td>{{ $inst->due_date->format('d M, Y') }}</td>
                                    <td>₨{{ number_format($inst->amount_due, 2) }}</td>
                                    <td class="text-success">₨{{ number_format($inst->amount_paid, 2) }}</td>
                                    <td class="text-danger">₨{{ number_format($inst->remaining_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $inst->status=='paid'?'bg-success':($inst->status=='partial'?'bg-warning text-dark':'bg-secondary') }}">
                                            {{ ucfirst($inst->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment History --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-history text-success me-2"></i>Payment History</h5>
                </div>
                <div class="card-body">
                    @if($sale->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Method</th>
                                    <th>Cheque/Ref</th>
                                    <th>Bank</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                    <td>
                                        {{ $payment->description ?? '—' }}
                                        @if($payment->installment_id)
                                        <br><small class="text-muted">Installment #{{ $payment->installment->installment_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                    </td>
                                    <td>{{ $payment->cheque_no ?? '—' }}</td>
                                    <td>{{ $payment->bank ?? '—' }}</td>
                                    <td class="text-success"><strong>₨{{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>
                                        <a href="{{ route('sales.payments.edit', [$project->slug, $sale->id, $payment->id]) }}"
                                            class="btn btn-sm btn-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteModal('deleteModal','{{ route('sales.payments.destroy', [$project->slug, $sale->id, $payment->id]) }}','Delete this payment?')"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="text-end"><strong>Total Received:</strong></td>
                                    <td colspan="2" class="text-success"><strong>₨{{ number_format($sale->payments->sum('amount'), 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No payments recorded yet</p>
                        <a href="{{ route('sales.payments.create', [$project->slug, $sale->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> Record First Payment
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection