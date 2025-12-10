@extends('layouts.home')

@section('title', 'Select Project - Junaid Builders')

@section('content')
<div class="project-header mb-4">
    <h3><i class="fas fa-folder-open text-primary me-2"></i> Your Projects - Total: {{ $projects->count() }}</h3>
    <p class="text-muted mb-0">Manage and monitor all your active & completed projects</p>
</div>

<div class="row g-4">
    @forelse($projects as $project)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="fw-bold mb-0 text-truncate">{{ $project->name }}</h5>
                    <span class="badge 
                            @if($project->status === 'active')
                                bg-success
                            @elseif($project->status === 'completed')
                                bg-primary
                            @elseif($project->status === 'on_hold')
                                bg-warning
                            @elseif($project->status === 'archived')
                                bg-secondary
                            @endif">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>

                <p class="text-muted mb-3 rich-truncate-2">{{ $project->description ?? 'No description available.' }}</p>

                <div class="d-flex justify-content-between text-muted small">
                    <span>Total Investment</span>
                    <span class="fw-semibold text-primary">
                        ₨ {{ number_format($project->members->sum('pivot.investment_amount')) }}
                    </span>
                </div>

                @if($project->status === 'completed')
                <div class="d-flex justify-content-between text-muted small">
                    <span>Profit</span>
                    <span class="fw-semibold text-success">
                        ₨ {{ number_format($project->profit ?? 0) }}
                    </span>
                </div>
                @else
                <div class="d-flex justify-content-between text-muted small">
                    <span>Units</span>
                    <span class="fw-semibold text-primary">{{ $project->units->count() }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between text-muted small">
                    <span>Progress</span>
                    <span class="fw-semibold text-primary">{{ $project->progress }}%</span>
                </div>

                <a href="{{ route('projects.select', $project->slug) }}" class="btn btn-primary w-100 mt-3">
                    <i class="fas fa-eye me-2"></i> View Project
                </a>
                <button class="btn w-100 btn-outline-secondary mt-1" data-bs-toggle="modal" data-bs-target="#editProjectModal-{{ $project->id }}" title="Edit Project">
                    <i class="fas fa-edit"></i> Edit Project
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="fas fa-folder-open fa-3x mb-3"></i>
        <p>No projects found. Start by adding your first project!</p>
        <a href="{{route('projects.create')}}" class="mx-auto btn btn-success btn-sm fw-semibold shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Create New Project
        </a>
    </div>
    @endforelse
</div>

{{-- Edit Project Modal --}}
@foreach($projects as $project)
<div class="modal fade" id="editProjectModal-{{ $project->id }}" tabindex="-1" aria-labelledby="editProjectModalLabel-{{ $project->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('projects.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel-{{ $project->id }}">
                        <i class="fas fa-edit text-primary me-2"></i> Edit Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_name_{{ $project->id }}" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name_{{ $project->id }}" name="name" value="{{ old('name', $project->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="edit_description_{{ $project->id }}" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description_{{ $project->id }}" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_start_date_{{ $project->id }}" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="edit_start_date_{{ $project->id }}" name="start_date" value="{{ old('start_date', $project->start_date) }}" required>
                            @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_end_date_{{ $project->id }}" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="edit_end_date_{{ $project->id }}" name="end_date" value="{{ old('end_date', $project->end_date) }}">
                            @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_total_investment_{{ $project->id }}" class="form-label">Total Investment</label>
                            <input type="number" disabled step="0.01" class="form-control @error('total_investment') is-invalid @enderror" id="edit_total_investment_{{ $project->id }}" name="total_investment" value="{{ old('total_investment', $project->total_investment) }}">
                            @error('total_investment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_land_cost_{{ $project->id }}" class="form-label">Land Cost</label>
                            <input type="number" step="0.01" class="form-control @error('land_cost') is-invalid @enderror" id="edit_land_cost_{{ $project->id }}" name="land_cost" oninput="currencyFormat(this,'curr-display-for-project-{{$project->id}}')" value="{{ old('land_cost', $project->land_cost) }}">
                            <small id="curr-display-for-project-{{$project->id}}" class="d-block"></small>
                            @error('land_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_sale_price_{{ $project->id }}" class="form-label">Sale Price</label>
                            <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror" id="edit_sale_price_{{ $project->id }}" name="sale_price" value="{{ old('sale_price', $project->sale_price) }}">
                            @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_status_{{ $project->id }}" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="edit_status_{{ $project->id }}" name="status" required>
                                <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="archived" {{ old('status', $project->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection