@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="auth-header">
    <i class="fas fa-box"></i>
    <h2>Inventory System</h2>
    <p class="mb-0">Sign in to your account</p>
</div>

<div class="auth-body">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Enter your password" required>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </button>
    </form>

    <hr class="my-4">

    <!-- <div class="text-center small text-muted">
        <p class="mb-1"><strong>Demo Credentials:</strong></p>
        <p class="mb-1"><strong>Super Admin:</strong> superadmin@inventory.com / password</p>
        <p class="mb-1"><strong>Admin:</strong> admin@inventory.com / password</p>
    </div> -->
</div>
@endsection
