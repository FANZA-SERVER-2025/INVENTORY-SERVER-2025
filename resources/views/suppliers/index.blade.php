@extends('layouts.app')

@section('title', 'Data Supplier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data Supplier</h1>
        <div>
            {{-- @can('export-suppliers')
            <a href="{{ route('suppliers.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            @endcan --}}
            @can('create-suppliers')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Supplier
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="suppliersTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Contact Person</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const canViewSuppliers = {{ auth()->user()->can('view-suppliers') ? 'true' : 'false' }};
    const canEditSuppliers = {{ auth()->user()->can('edit-suppliers') ? 'true' : 'false' }};
    const canDeleteSuppliers = {{ auth()->user()->can('delete-suppliers') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/datatable-crud.js') }}"></script>
<script src="{{ asset('js/suppliers.js') }}"></script>
@endpush
@endsection
