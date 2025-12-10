@extends('layouts.app')

@section('title', 'Settings - Junaid Builders')

@section('content')
<h3 class="mb-4">Settings</h3>


{{-- Predefined Categories Section --}}
<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tags text-secondary me-2"></i> Predefined Categories
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus me-1"></i> Add Category
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Category Name</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($category->name) }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCategoryModal-{{ $category->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteModal('deleteModal','{{ route('settings.categories.destroy', $category) }}','Are you sure you want to delete this category?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted text-center">No categories found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Predefined Units Section --}}
<section>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cube text-secondary me-2"></i> Predefined Units
                </h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="fas fa-plus me-1"></i> Add Unit
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Sr.No</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th class="text-end">Cost Price</th>
                            <th class="text-end">Default Sale Price</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ ucfirst($unit->type) }}</td>
                            <td>{{ $unit->size }}</td>
                            <td class="text-end">₨ {{ number_format($unit->cost_price, 2) }}</td>
                            <td class="text-end">₨ {{ number_format($unit->default_sale_price, 2) }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editUnitModal-{{ $unit->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteModal('deleteModal','{{ route('settings.units.destroy', $unit) }}','Are you sure you want to delete this unit?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">No units found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="category_name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Unit Modal --}}
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.units.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_type" class="form-label">Type</label>
                        <input type="text" class="form-control @error('type') is-invalid @enderror" id="unit_type" name="type" value="{{ old('type') }}" required>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_size" class="form-label">Size</label>
                        <input type="text" class="form-control @error('size') is-invalid @enderror" id="unit_size" name="size" value="{{ old('size') }}" required>
                        @error('size')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_cost_price" class="form-label">Cost Price</label>
                        <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" id="unit_cost_price" name="cost_price" value="{{ old('cost_price') }}" required>
                        @error('cost_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_default_sale_price" class="form-label">Default Sale Price</label>
                        <input type="number" step="0.01" class="form-control @error('default_sale_price') is-invalid @enderror" id="unit_default_sale_price" name="default_sale_price" value="{{ old('default_sale_price') }}" required>
                        @error('default_sale_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Unit Modal --}}
@foreach($units as $unit)
<div class="modal fade" id="editUnitModal-{{ $unit->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_type_{{ $unit->id }}" class="form-label">Type</label>
                        <input type="text" class="form-control @error('type') is-invalid @enderror" id="edit_type_{{ $unit->id }}" name="type" value="{{ old('type', $unit->type) }}" required>
                        @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_size_{{ $unit->id }}" class="form-label">Size</label>
                        <input type="text" class="form-control @error('size') is-invalid @enderror" id="edit_size_{{ $unit->id }}" name="size" value="{{ old('size', $unit->size) }}" required>
                        @error('size')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_cost_price_{{ $unit->id }}" class="form-label">Cost Price</label>
                        <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" id="edit_cost_price_{{ $unit->id }}" name="cost_price" value="{{ old('cost_price', $unit->cost_price) }}" required>
                        @error('cost_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_default_sale_price_{{ $unit->id }}" class="form-label">Default Sale Price</label>
                        <input type="number" step="0.01" class="form-control @error('default_sale_price') is-invalid @enderror" id="edit_default_sale_price_{{ $unit->id }}" name="default_sale_price" value="{{ old('default_sale_price', $unit->default_sale_price) }}" required>
                        @error('default_sale_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
{{-- Edit Category Modal --}}
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal-{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_{{ $category->id }}" class="form-label">Category Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name_{{ $category->id }}" name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection