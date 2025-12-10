@extends('layouts.app')

@section('title', 'Clients - Junaid Builders')

@section('content')
<h3 class="mb-4">Clients</h3>

{{-- Create Client Form --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-user-plus text-primary me-2"></i> Add Client</h5>

            <form method="POST" class="row g-3 needs-validation" novalidate
                action="{{ route('clients.store', session('active_project_slug')) }}">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Client Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Ali Khan" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="e.g. 03001234567">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="cnic" value="{{ old('cnic') }}" class="form-control" placeholder="e.g. 35201-1234567-8">
                    @error('cnic') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-control" placeholder="Client Address">
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" class="form-control" placeholder="Additional notes (optional)">
                    @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i> Save Client</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Clients List --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="fas fa-list text-secondary me-2"></i> Client Records</h5>

                {{-- Filter --}}
                <form method="GET" class="d-flex flex-md-row flex-column gap-1"
                    action="{{ route('clients.index', $project->slug) }}">

                    {{-- Search --}}
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search Client..." class="form-control form-control-sm" style="min-width: 180px;" />

                    {{-- Has Sales Filter --}}
                    <select name="has_sales" class="form-select form-select-sm" style="min-width: 160px;">
                        <option value="all" {{ request('has_sales') === 'all' || !request('has_sales') ? 'selected' : '' }}>
                            All Clients
                        </option>
                        <option value="yes" {{ request('has_sales') === 'yes' ? 'selected' : '' }}>
                            Clients with Sales
                        </option>
                    </select>

                    {{-- Buttons --}}
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i>
                    </button>

                    @if(request()->hasAny(['search', 'has_sales']))
                    <a href="{{ route('clients.index', $project->slug) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </form>

            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>CNIC</th>
                            <th>Notes</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td>{{ $clients->firstItem() + $loop->index }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->phone ?? '—' }}</td>
                            <td>{{ $client->cnic ?? '—' }}</td>
                            <td>{{ Str::limit($client->notes, 40, '...') ?? '—' }}</td>
                            <td class="text-center text-nowrap">
                                <a class="btn btn-sm btn-info" href="{{ route('clients.show', [session('active_project_slug'),$client]) }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editClientModal-{{ $client->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="deleteModal('deleteModal', '{{ route('clients.destroy', ['client' => $client, 'project' => session('active_project_slug')]) }}', 'Are you sure to delete this client?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a class="btn btn-sm btn-primary" href="{{ route('reports.client', [session('active_project_slug'),$client]) }}">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-center text-muted">No Clients Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="100%">{{ $clients->links('pagination::bootstrap-5') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Edit Modals --}}
@foreach($clients as $client)
<div class="modal fade" id="editClientModal-{{ $client->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" class="needs-validation" novalidate
                action="{{ route('clients.update', ['client' => $client, 'project' => session('active_project_slug')]) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CNIC</label>
                            <input type="text" name="cnic" value="{{ old('cnic', $client->cnic) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" value="{{ old('address', $client->address) }}" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Client</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection