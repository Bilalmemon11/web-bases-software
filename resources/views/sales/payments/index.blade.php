@extends('layouts.app')
@section('title', 'Payments for Sale #' . $sale->id)

@section('content')
<section>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Payments - Sale #{{ $sale->id }}</h4>
            <p class="text-muted mb-0">Client: <strong>{{ $sale->client->name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('sales.show', [$project->slug, $sale->id]) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Sale
            </a>
            <a href="{{ route('payments.create', [$project->slug, $sale->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Payment
            </a>
        </div>
    </div>

    {{-- Payment Summary Card --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Amount</h6>
                    <h4 class="mb-0">₨ {{ number_format($sale->total_amount, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Discount</h6>
                    <h4 class="mb-0 text-info">₨ {{ number_format($sale->discount, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Paid Amount</h6>
                    <h4 class="mb-0 text-success">₨ {{ number_format($sale->paid_amount, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Remaining</h6>
                    <h4 class="mb-0 text-danger">₨ {{ number_format($sale->pending_amount, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave text-success me-2"></i>Payment History</h5>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Payment Method</th>
                            <th>Cheque No.</th>
                            <th>Bank</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>{{ $payment->description ?? '—' }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td>{{ $payment->cheque_no ?? '—' }}</td>
                            <td>{{ $payment->bank_name ?? '—' }}</td>
                            <td class="text-end fw-bold">₨ {{ number_format($payment->amount, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('payments.edit', [$project->slug, $sale->id, $payment->id]) }}" 
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('payments.destroy', [$project->slug, $sale->id, $payment->id]) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end">Total Paid:</td>
                            <td class="text-end">₨ {{ number_format($payments->sum('amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted">No payments recorded yet.</p>
                <a href="{{ route('payments.create', [$project->slug, $sale->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add First Payment
                </a>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection