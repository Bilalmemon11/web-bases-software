@extends('layouts.app')
@section('title', 'Sales Management')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4>Sales Management</h4>
    <a href="{{ route('sales.create', $project->slug) }}" class="btn btn-success"><i class="fas fa-plus-circle me-1"></i> New Sale</a>
</div>

<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="fas fa-list text-secondary me-2"></i> Sales List</h5>
                <select id="filterStatus" style="width: max-content;" class="form-select form-select-sm" onchange="location.href='?status='+this.value">
                    <option value="All" {{ request('status')=='All'?'selected':'' }}>All Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status')==$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Sale ID</th>
                            <th>Client</th>
                            <th>Units</th>
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sales->firstItem() + $loop->index }}</td>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->client->name }}</td>
                            <td>{{ $sale->units->pluck('unit_no')->join(', ') }}</td>
                            <td>₨ {{ number_format($sale->total_amount,2) }}</td>
                            <td>₨ {{ number_format($sale->paid_amount,2) }}</td>
                            <td>₨ {{ number_format($sale->remaining_amount,2) }}</td>
                            <td>
                                <span class="badge {{ $sale->status=='sold'?'bg-success':($sale->status=='reserved'?'bg-warning text-dark':'bg-secondary') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('sales.show', [$project->slug,$sale->id]) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sales.edit', [$project->slug,$sale->id]) }}" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteModal('deleteModal','{{ route('sales.destroy', [$project->slug, $sale->id]) }}','Are you sure to delete this sale?')"><i class="fas fa-trash"></i></button>
                                <a class="btn btn-sm btn-primary" href="{{ route('reports.sale', [session('active_project_slug'),$sale]) }}">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No Sales Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

{{ $sales->links('pagination::bootstrap-5') }}
@endsection