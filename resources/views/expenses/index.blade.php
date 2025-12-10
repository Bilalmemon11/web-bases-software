@extends('layouts.app')

@section('title', 'Expenses - Junaid Builders')

@section('content')
<h3 class="mb-4">Expenses</h3>
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i> Add Expense</h5>
            <form class="row g-3" novalidate enctype="multipart/form-data"
                class="row g-3 needs-validation"
                method="POST"
                action="{{ route('expenses.store', session('active_project_slug')) }}">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control" />
                    @error('date')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label">Category</label>
                        <button type="button" onclick="toggleClassMulti(['expense_type','new_expense_type'],'d-none');setNull('expense_type')" class="btn btn-sm bg-transparent p-0 fs-6 text-success"><i class="fas fa-plus-circle"></i> <small>New</small></button>
                    </div>
                    <select class="form-select" id="expense_type" name="expense_type">
                        <option value="">Select</option>
                        @foreach($category as $cat)
                        <option value="{{$cat->name}}" {{ old('expense_type') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('expense_type')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <input type="text" id="new_expense_type" value="{{old('new_expense_type')}}" name="new_expense_type" style="text-transform: capitalize;" class="form-control d-none" placeholder="e.g. Client's lunch" />
                    @error('new_expense_type')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" name="description" value="{{ old('description') }}" placeholder="Details of expense" />
                    @error('description')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">₨</span>
                        <input type="number" class="form-control" value="{{old('amount')}}" oninput="currencyFormat(this,'curr-display')" name="amount" placeholder="e.g., 20000" />
                    </div>
                    <small id="curr-display" class="d-block"></small>
                    @error('amount')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Attachment/Receipt (optional)</label>
                    <input type="file" class="form-control" name="attachment" />
                    @error('attachment')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Save Expense</button>
                </div>
            </form>
        </div>
    </div>
    <small class="text-muted d-block mt-2">Note: Land cost from project is auto-inserted as the first expense.</small>
</section>

<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="fas fa-list text-secondary me-2"></i> Expense Records</h5>
                <form method="GET" class="d-flex flex-md-row flex-column gap-1" action="{{ route('expenses.index', $project->slug) }}">
                    {{-- Category Filter --}}
                    <select name="category" class="form-select form-select-sm" style="min-width: 120px;">
                        <option value="All" {{ request('category') == 'All' ? 'selected' : '' }}>All Categories</option>
                        @foreach($category as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                            {{ ucfirst($cat->name) }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Date Filter --}}
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" style="min-width: 130px;" />

                    {{-- Search Box --}}
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="form-control form-control-sm" style="min-width: 150px;" />

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i>
                    </button>

                    {{-- Reset Button --}}
                    @if(request()->hasAny(['category', 'date', 'search']))
                    <a href="{{ route('expenses.index', $project->slug) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </form>

            </div>
            <div class="table-responsive">
                <table class="table sortable-table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Attachment</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expenses->firstItem() + $loop->index }}</td>
                            <td>{{date('d-m-Y', strtotime($expense->expense_date))}}</td>
                            <td>{{$expense->category}}</td>
                            <td class="text-truncate" style="max-width: 150px;">{{$expense->description}}</td>
                            <td class="text-end">₨ {{ number_format($expense->amount, 2) }}</td>
                            <td class="text-center">
                                @if($expense->attachment)
                                <a href="{{ asset('storage/' . $expense->attachment) }}" download="{{$project->name.$expense->type.$expense->created_at}}"><i class="fas fa-paperclip"></i></a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editExpenseModal-{{$expense->id}}"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteModal('deleteModal','{{ route('expenses.destroy', ['expense' => $expense,'project' => session('active_project_slug')]) }}','Are you sure to remove this expense from the project?')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-muted text-center">No Records Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="100%">
                                {{ $expenses->links('pagination::bootstrap-5') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
@push('modals')
@foreach($expenses as $expense)
<!-- Edit expense Modal -->
<div class="modal fade" id="editExpenseModal-{{$expense->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" class="needs-validation" novalidate action="{{route('expenses.update', ['expense' => $expense,'project' => session('active_project_slug')])}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>Edit Expense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{old('date', date('Y-m-d', strtotime($expense->expense_date)))}}" class="form-control" required>
                            @error('date')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label">Category</label>
                                <button type="button" onclick="toggleClassMulti(['expense_type_{{$expense->id}}','new_expense_type_{{$expense->id}}'],'d-none');setNull('expense_type_{{$expense->id}}')" class="btn btn-sm bg-transparent p-0 fs-6 text-success"><i class="fas fa-plus-circle"></i> <small>New</small></button>
                            </div>
                            <select class="form-select" id="expense_type_{{$expense->id}}" name="expense_type">
                                <option value="">Select</option>
                                @foreach($category as $cat)
                                <option value="{{$cat->name}}" {{ $expense->category == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('expense_type')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <input type="text" id="new_expense_type_{{$expense->id}}" value="{{old('new_expense_type', $expense->new_expense_type)}}" name="new_expense_type" style="text-transform: capitalize;" class="form-control d-none" placeholder="e.g. Client's lunch" />
                            @error('new_expense_type')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" value="{{ old('description', $expense->description) }}" placeholder="Details of expense" />
                            @error('description')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" class="form-control" value="{{old('amount',$expense->amount)}}" oninput="currencyFormat(this,'curr-display-{{$expense->id}}')" name="amount" placeholder="e.g., 20000" />
                            </div>
                            <small id="curr-display-{{$expense->id}}" class="d-block"></small>
                            @error('amount')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment/Receipt (optional)</label>
                            <input type="file" class="form-control" name="attachment" />
                            @error('attachment')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Updating this member will recalculate all members' profit shares based on new investment amounts.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endpush