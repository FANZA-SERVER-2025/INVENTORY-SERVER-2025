@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="auth-header">
    <i class="fas fa-user-plus"></i>
    <h2>Create Account</h2>
    <p class="mb-0">Join our inventory system</p>
</div>

<div class="auth-body">
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

    <form action="{{ route('register') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}" placeholder="Enter your full name" required>
            </div>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" placeholder="Enter your email" required>
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number (Optional)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                       value="{{ old('phone') }}" placeholder="Enter your phone number">
            </div>
            @error('phone')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Address (Optional)</label>
            <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                      rows="2" placeholder="Enter your address">{{ old('address') }}</textarea>
            @error('address')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Enter password (min. 8 characters)" required>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password_confirmation" class="form-control" 
                       placeholder="Confirm your password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="fas fa-user-plus me-2"></i>Sign Up
        </button>

        <div class="text-center">
            <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Sign In</a></p>
        </div>
    </form>
</div>
@endsection
