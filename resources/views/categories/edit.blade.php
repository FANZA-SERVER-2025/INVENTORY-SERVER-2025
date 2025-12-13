@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-edit me-2"></i>Edit Category
    </h1>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to List
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit me-2"></i>Category Information
            </div>
            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}" 
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
                               value="{{ old('code', $category->code) }}" 
                               placeholder="Enter category code"
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
                                  placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
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
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
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
                            <i class="fas fa-save me-2"></i>Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Category Info
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td>{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Items Count:</strong></td>
                        <td>
                            <span class="badge bg-info">{{ $category->items->count() }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $category->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Warning
            </div>
            <div class="card-body">
                <p class="mb-0 small">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    Changing the code will affect related items and reports.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
