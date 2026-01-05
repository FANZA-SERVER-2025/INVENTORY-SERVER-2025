@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-tags me-2"></i>Categories
    </h1>
    <div>
        {{-- @can('export-categories')
            <a href="{{ route('categories.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
        @endcan --}}
        @can('create-categories')
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Category
            </a>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="categoriesTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Items Count</th>
                        <th>Created At</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/categories.css') }}">
@endpush

@push('scripts')
<script>
    const canViewCategories = {{ auth()->user()->can('view-categories') ? 'true' : 'false' }};
    const canEditCategories = {{ auth()->user()->can('edit-categories') ? 'true' : 'false' }};
    const canDeleteCategories = {{ auth()->user()->can('delete-categories') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/categories.js') }}"></script>
@endpush
