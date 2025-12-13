@extends('layouts.app')

@section('title', 'View Category')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-tags me-2"></i>Category Details
    </h1>
    <div>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
        @can('edit-categories')
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>Category Information
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>ID:</strong></td>
                        <td>{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Code:</strong></td>
                        <td><span class="badge bg-secondary">{{ $category->code }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Description:</strong></td>
                        <td>{{ $category->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Items Count:</strong></td>
                        <td>
                            <span class="badge bg-info">{{ $category->items->count() }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created At:</strong></td>
                        <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Updated At:</strong></td>
                        <td>{{ $category->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-box me-2"></i>Items in this Category</span>
                @can('create-items')
                    <a href="{{ route('items.create') }}?category_id={{ $category->id }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Item
                    </a>
                @endcan
            </div>
            <div class="card-body">
                @if($category->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Supplier</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->items as $item)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $item->code }}</span></td>
                                    <td>
                                        @if($item->image)
                                            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" class="rounded me-2" width="32" height="32">
                                        @endif
                                        {{ $item->name }}
                                    </td>
                                    <td>{{ $item->supplier->name }}</td>
                                    <td>
                                        @if($item->isLowStock())
                                            <span class="badge bg-danger">{{ $item->stock }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $item->stock }}</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('view-items')
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('edit-items')
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No items in this category yet.</p>
                        @can('create-items')
                            <a href="{{ route('items.create') }}?category_id={{ $category->id }}" class="btn btn-primary\">
                                <i class="fas fa-plus me-2"></i>Add First Item
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
