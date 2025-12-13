@extends('layouts.app')

@section('title', 'Data Role')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data Role & Permissions</h1>
        <div>
            @can('create-roles')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Role
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="rolesTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Role</th>
                            <th>Jumlah Permission</th>
                            <th>Jumlah User</th>
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
    const canViewRoles = {{ auth()->user()->can('view-roles') ? 'true' : 'false' }};
    const canEditRoles = {{ auth()->user()->can('edit-roles') ? 'true' : 'false' }};
    const canDeleteRoles = {{ auth()->user()->can('delete-roles') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/roles.js') }}"></script>
@endpush
@endsection
