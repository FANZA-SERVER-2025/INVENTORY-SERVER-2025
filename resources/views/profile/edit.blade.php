@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Edit Profil</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                <i class="fas fa-user"></i> Informasi Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                                <i class="fas fa-lock"></i> Ubah Password
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Profile Information Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <label for="name" class="col-sm-3 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="email" class="col-sm-3 col-form-label">Email <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="phone" class="col-sm-3 col-form-label">Telepon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="address" class="col-sm-3 col-form-label">Alamat</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="avatar" class="col-sm-3 col-form-label">Avatar</label>
                                    <div class="col-sm-9">
                                        @if($user->avatar)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                 class="rounded-circle" 
                                                 style="width: 100px; height: 100px; object-fit: cover;"
                                                 alt="Current Avatar"
                                                 id="currentAvatar">
                                        </div>
                                        @endif
                                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                               id="avatar" name="avatar" accept="image/*">
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                                        <div id="avatarPreview" class="mt-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <form action="{{ route('profile.password') }}" method="POST" id="passwordForm">
                                @csrf
                                @method('PUT')

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Password harus minimal 8 karakter dan berisi kombinasi huruf dan angka
                                </div>

                                <div class="row mb-3">
                                    <label for="current_password" class="col-sm-3 col-form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group @error('current_password') is-invalid @enderror">
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                   id="current_password" name="current_password" autocomplete="current-password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password" class="col-sm-3 col-form-label">Password Baru <span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="input-group @error('password') is-invalid @enderror">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" autocomplete="new-password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text" id="passwordStrength"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Ubah Password
                                        </button>
                                        <button type="reset" class="btn btn-secondary" id="resetPasswordForm">
                                            <i class="fas fa-redo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Akun</h5>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                             class="rounded-circle mb-3" 
                             style="width: 120px; height: 120px; object-fit: cover;"
                             alt="{{ $user->name }}">
                    @else
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 120px; height: 120px; font-size: 48px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="mb-3">
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </div>

                    <hr>

                    <table class="table table-sm table-borderless text-start mb-0">
                        <tr>
                            <td class="text-muted">Telepon:</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bergabung:</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir Update:</td>
                            <td>{{ $user->updated_at->diffForHumans() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-shield-alt"></i> Keamanan</h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Gunakan password yang kuat dan unik</li>
                        <li>Jangan bagikan password kepada siapapun</li>
                        <li>Ubah password secara berkala</li>
                        <li>Logout jika menggunakan komputer bersama</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto switch to password tab if there are password errors
    @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
        $('#password-tab').tab('show');
    @endif

    // Avatar preview
    $('#avatar').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatarPreview').html(`
                    <div>
                        <strong>Preview:</strong><br>
                        <img src="${e.target.result}" class="rounded-circle mt-2" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                `);
            }
            reader.readAsDataURL(file);
        }
    });

    // Toggle password visibility
    $(document).on('click', '.toggle-password', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = $(this);
        const targetId = button.attr('data-target');
        const $input = $('#' + targetId);
        const icon = button.find('i');
        
        if (!$input.length) {
            console.error('Input not found:', targetId);
            return;
        }
        
        const currentType = $input.attr('type');
        const currentValue = $input.val();
        
        if (currentType === 'password') {
            // Change to text
            $input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            // Change to password
            $input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
        
        // Ensure value persists and input gets focus back
        $input.val(currentValue);
        $input.focus();
        
        // Move cursor to end
        const elem = $input[0];
        if (elem.setSelectionRange) {
            const len = currentValue.length;
            elem.setSelectionRange(len, len);
        }
    });

    // Password strength checker
    $('#password').on('input', function() {
        const password = $(this).val();
        
        if (password.length === 0) {
            $('#passwordStrength').html('');
            return;
        }
        
        let strength = 0;
        let message = '';
        let color = '';

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        switch(strength) {
            case 0:
            case 1:
                message = 'Lemah';
                color = 'text-danger';
                break;
            case 2:
            case 3:
                message = 'Sedang';
                color = 'text-warning';
                break;
            case 4:
            case 5:
                message = 'Kuat';
                color = 'text-success';
                break;
        }

        $('#passwordStrength').html(`Kekuatan password: <strong class="${color}">${message}</strong>`);
    });

    // Form validation
    $('#passwordForm').on('submit', function(e) {
        // No additional validation needed since confirmation is removed
    });

    // Reset form handler
    $('#resetPasswordForm').on('click', function() {
        $('#passwordStrength').html('');
    });
});
</script>
@endpush
@endsection
