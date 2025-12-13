@extends('layouts.app')

@section('title', 'Add Category')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-tags me-2"></i>Add New Category
    </h1>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus-circle me-2"></i>Category Information
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="Enter category name"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}" 
                               placeholder="Enter category code (e.g., CAT001)"
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Unique code for this category</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Enter category description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Status
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive categories will be hidden from selection</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Tips
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Use clear and descriptive names
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Code should be unique and easy to remember
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Add detailed description for clarity
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Inactive categories won't appear in dropdowns
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate code from name (optional)
    $('#name').on('input', function() {
        if ($('#code').val() === '') {
            var code = $(this).val()
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 6);
            $('#code').val('CAT' + code);
        }
    });
});
</script>
@endpush
