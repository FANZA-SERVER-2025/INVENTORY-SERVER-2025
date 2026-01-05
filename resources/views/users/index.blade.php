@extends('layouts.app')

@section('title', 'Data User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Data User</h1>
        <div>
            {{-- @can('export-users')
            <a href="{{ route('users.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            @endcan --}}
            @can('create-users')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah User
            </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="usersTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Role</th>
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
    const canViewUsers = {{ auth()->user()->can('view-users') ? 'true' : 'false' }};
    const canEditUsers = {{ auth()->user()->can('edit-users') ? 'true' : 'false' }};
    const canDeleteUsers = {{ auth()->user()->can('delete-users') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/users.js') }}"></script>
@endpush
@endsection
