@extends('layouts.app')

@section('title', 'Detail Role')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Role: {{ $role->name }}</h1>
        <div>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @can('edit-roles')
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Role</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">ID:</td>
                            <td>{{ $role->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama:</td>
                            <td><span class="badge bg-primary">{{ $role->name }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jumlah Permission:</td>
                            <td>{{ $role->permissions->count() }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Jumlah User:</td>
                            <td>{{ $role->users->count() }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat:</td>
                            <td>{{ $role->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diperbarui:</td>
                            <td>{{ $role->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">User dengan Role Ini</h5>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($role->users as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $user->name }}
                                    @can('view-users')
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center py-3">Tidak ada user dengan role ini</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Permissions</h5>
                </div>
                <div class="card-body">
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                            return explode('-', $permission->name)[1] ?? 'other';
                        });
                    @endphp

                    @if($groupedPermissions->count() > 0)
                        @foreach($groupedPermissions as $module => $permissions)
                            <div class="mb-4">
                                <h6 class="fw-bold text-uppercase mb-3">
                                    <i class="fas fa-folder text-primary"></i> {{ ucfirst($module) }}
                                </h6>
                                <div class="row">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4 mb-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> {{ str_replace('-', ' ', $permission->name) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Role ini belum memiliki permissions</p>
                            @can('edit-roles')
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Permissions
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
