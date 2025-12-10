@extends('layouts.app')

@section('title', 'Units - Junaid Builders')

@section('content')
<h3 class="mb-4">Units</h3>
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i> Add Unit</h5>
            <form class="row g-3 needs-validation" novalidate method="POST"
                action="{{ route('units.store', session('active_project_slug')) }}">
                @csrf

                {{-- Unit Type --}}
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label">Unit Type</label>
                        <button type="button"
                            onclick="toggleClassMulti(['unit_type','new_unit_type'],'d-none');setNull('unit_type')"
                            class="btn btn-sm bg-transparent p-0 fs-6 text-success">
                            <i class="fas fa-plus-circle"></i> <small>New</small>
                        </button>
                    </div>
                    <select class="form-select" id="unit_type" name="unit_type">
                        <option value="">Select</option>
                        @foreach($predefinedUnits as $type)
                        <option value="{{ $type }}" {{ old('unit_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                        @endforeach
                    </select>
                    @error('unit_type')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror

                    <input type="text" id="new_unit_type" name="new_unit_type"
                        value="{{ old('new_unit_type') }}"
                        class="form-control d-none mt-2"
                        placeholder="e.g. Penthouse, Studio" />
                    @error('new_unit_type')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Size --}}
                <div class="col-md-4">
                    <label class="form-label">Size</label>
                    <input type="text" class="form-control" name="size"
                        value="{{ old('size') }}" placeholder="e.g., 1200 sq.ft" />
                    @error('size')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Sale Price --}}
                <div class="col-md-4">
                    <label class="form-label">Sale Price</label>
                    <div class="input-group">
                        <span class="input-group-text">₨</span>
                        <input type="number" step="0.01" class="form-control" name="sale_price"
                            value="{{ old('sale_price') }}" placeholder="e.g., 1200000" oninput="currencyFormat(this,'curr-display')" />
                    </div>
                    @error('sale_price')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <small id="curr-display"></small>
                </div>

                {{-- QTY --}}
                <div class="col-md-4">
                    <label class="form-label">Qty</label>
                    <input type="number" class="form-control" name="qty"
                        value="{{ old('qty') }}" min="1" placeholder="e.g. 1" />
                    @error('qty')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Unit
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- Header and Filters --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building text-secondary me-2"></i> Unit Records
                </h5>

                {{-- Filters --}}
                <form method="GET" class="d-flex flex-md-row flex-column gap-1" action="{{ route('units.index', $project->slug) }}">
                    {{-- Unit Type --}}
                    <select name="type" class="form-select form-select-sm" style="min-width: 140px;">
                        <option value="All" {{ request('type') == 'All' ? 'selected' : '' }}>All Types</option>
                        @foreach($predefinedUnits as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Status --}}
                    <select name="status" class="form-select form-select-sm" style="min-width: 140px;">
                        <option value="All" {{ request('status') == 'All' ? 'selected' : '' }}>All Statuses</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Search --}}
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Unit..." class="form-control form-control-sm" style="min-width: 160px;" />

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i>
                    </button>

                    {{-- Reset --}}
                    @if(request()->hasAny(['type', 'status', 'search']))
                    <a href="{{ route('units.index', $project->slug) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Unit No</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Sold At</th>
                            <th>Status</th>
                            <th>Client</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units->groupBy('type') as $type => $groupedUnits)
                        {{-- Group Header Row --}}
                        <tr class="table-light border-top">
                            <td colspan="9" class="fw-semibold text-primary text-center">
                                <i class="fas fa-layer-group me-2"></i> {{ strtoupper($type) }}
                            </td>
                        </tr>

                        {{-- Units Under This Type --}}
                        @foreach($groupedUnits as $unit)
                        <tr>
                            <td>{{ $units->firstItem() + $loop->parent->index + $loop->index }}</td>
                            <td>{{ $unit->unit_no }}</td>
                            <td>{{ ucfirst($unit->type) }}</td>
                            <td>{{ $unit->size }}</td>
                            <td class="text-end">₨ {{ number_format($unit->sale_price, 2) }}</td>
                            <td class="text-center">
                                {{ $unit->soldSale()?->sale_date?->format('d-M-Y') ?? $unit->reservedSale()?->sale_date?->format('d-M-Y') ?? '—' }}
                            </td>
                            <td>
                                <span class="badge 
                               {{ $unit->status == 'sold' ? 'bg-danger' : 
                              ($unit->status == 'reserved' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $unit->soldTo()?->name ?? $unit->reservedBy()?->name ?? '—' }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUnitModal-{{ $unit->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="deleteModal('deleteModal','{{ route('units.destroy', ['unit' => $unit, 'project' => session('active_project_slug')]) }}','Are you sure you want to delete this unit?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="100%" class="text-muted text-center">No Units Found</td>
                        </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="100%">
                                {{ $units->links('pagination::bootstrap-5') }}
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
{{-- Edit Unit Modals --}}
@foreach($units as $unit)
<div class="modal fade" id="editUnitModal-{{ $unit->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" class="needs-validation" novalidate
                action="{{ route('units.update', ['unit' => $unit, 'project' => session('active_project_slug')]) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Unit ({{ $unit->unit_no }})
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Unit Type (with new option toggle) --}}
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label">Unit Type</label>
                                <button type="button"
                                    onclick="toggleClassMulti(['unit_type_{{ $unit->id }}','new_unit_type_{{ $unit->id }}'],'d-none');setNull('unit_type_{{ $unit->id }}')"
                                    class="btn btn-sm bg-transparent p-0 fs-6 text-success">
                                    <i class="fas fa-plus-circle"></i> <small>New</small>
                                </button>
                            </div>

                            <select name="unit_type" id="unit_type_{{ $unit->id }}" class="form-select">
                                <option value="">Select</option>
                                @foreach($predefinedUnits as $type)
                                <option value="{{ $type }}" {{ $unit->type == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                                @endforeach
                            </select>
                            @error('unit_type') <small class="text-danger">{{ $message }}</small> @enderror

                            <input type="text" name="new_unit_type" id="new_unit_type_{{ $unit->id }}"
                                value="{{ old('new_unit_type') }}"
                                class="form-control d-none mt-2" placeholder="e.g. Penthouse, Studio" />
                            @error('new_unit_type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Size --}}
                        <div class="col-md-4">
                            <label class="form-label">Size</label>
                            <input type="text" name="size" class="form-control" value="{{ old('size', $unit->size) }}" placeholder="e.g., 1200 sq.ft">
                        </div>

                        {{-- Sale Price --}}
                        <div class="col-md-6">
                            <label class="form-label">Sale Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" step="0.01" name="sale_price" class="form-control" oninput="currencyFormat(this,'curr-display-{{$unit->id}}')"
                                    value="{{ old('sale_price', $unit->sale_price) }}" placeholder="e.g., 1200000">
                            </div>
                            <small id="curr-display-{{$unit->id}}"></small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Unit
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach
@endpush