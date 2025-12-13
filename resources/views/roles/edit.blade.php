@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Role: {{ $role->name }}</h1>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h5 class="mb-3">Permissions</h5>
                        <p class="text-muted">Pilih permissions yang akan diberikan ke role ini:</p>

                        @foreach($permissions as $module => $modulePermissions)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" 
                                               id="module_{{ $module }}" data-module="{{ $module }}">
                                        <label class="form-check-label fw-bold" for="module_{{ $module }}">
                                            {{ ucfirst($module) }} ({{ $modulePermissions->count() }} permissions)
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($modulePermissions as $permission)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" 
                                                           type="checkbox" name="permissions[]" 
                                                           value="{{ $permission->name }}" 
                                                           id="permission_{{ $permission->id }}"
                                                           data-module="{{ $module }}"
                                                           {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ str_replace('-', ' ', $permission->name) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @error('permissions')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Check module checkbox if all permissions are checked
    $('.module-checkbox').each(function() {
        let module = $(this).data('module');
        let totalPermissions = $(`.permission-checkbox[data-module="${module}"]`).length;
        let checkedPermissions = $(`.permission-checkbox[data-module="${module}"]:checked`).length;
        
        if (totalPermissions === checkedPermissions && totalPermissions > 0) {
            $(this).prop('checked', true);
        }
    });

    // Module checkbox handler
    $('.module-checkbox').on('change', function() {
        let module = $(this).data('module');
        let isChecked = $(this).is(':checked');
        $(`.permission-checkbox[data-module="${module}"]`).prop('checked', isChecked);
    });

    // Update module checkbox when individual permissions change
    $('.permission-checkbox').on('change', function() {
        let module = $(this).data('module');
        let totalPermissions = $(`.permission-checkbox[data-module="${module}"]`).length;
        let checkedPermissions = $(`.permission-checkbox[data-module="${module}"]:checked`).length;
        
        $(`#module_${module}`).prop('checked', totalPermissions === checkedPermissions);
    });
});
</script>
@endpush
@endsection
