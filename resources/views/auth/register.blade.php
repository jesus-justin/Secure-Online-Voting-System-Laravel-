@extends('layouts.app')

@section('title', 'Register - Secure Voting')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); position: relative; overflow: hidden;">
    <!-- Animated background elements -->
    <div style="position: absolute; top: -10%; right: -5%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,255,255,0.1), transparent); border-radius: 50%; animation: float 7s ease-in-out infinite;"></div>
    <div style="position: absolute; bottom: -10%; left: -5%; width: 450px; height: 450px; background: radial-gradient(circle, rgba(255,255,255,0.08), transparent); border-radius: 50%; animation: float 9s ease-in-out infinite reverse;"></div>
    
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <div class="mb-3">
                                <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center shadow" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-person-plus-fill text-white" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h2 class="card-title text-dark fw-bold mb-2">Join Secure Voting</h2>
                            <p class="text-muted">Create your account to participate in secure elections</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Registration Error!</strong>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" id="registerForm">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-5">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-person text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-lg border-0 @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-5">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-envelope text-primary"></i>
                                    </span>
                                    <input type="email" class="form-control form-control-lg border-0 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-5">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-lock text-primary"></i>
                                    </span>
                                    <input type="password" class="form-control form-control-lg border-0 @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Create a strong password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle"></i> Minimum 8 characters required
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-5">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-lock text-primary"></i>
                                    </span>
                                    <input type="password" class="form-control form-control-lg border-0" 
                                           id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                                </div>
                            </div>

                            <div class="alert alert-info alert-sm mb-4">
                                <i class="bi bi-info-circle"></i>
                                <small>After registration, an administrator will verify your account before you can vote.</small>
                            </div>

                            <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                            <button type="submit" class="btn btn-success btn-lg w-100 mb-3 fw-bold">
                                <i class="bi bi-person-check"></i> Create Account
                            </button>

                        <hr>

                        <div class="text-center">
                            <p class="mb-0 text-muted">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                                    Sign in here
                                </a>
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <p class="text-center text-muted small mb-0">
                                <i class="bi bi-shield-lock text-success"></i> 
                                Your data is encrypted and secure
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('landing') }}" class="text-white text-decoration-none small">
                        <i class="bi bi-arrow-left"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simple form submission without reCAPTCHA for now
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    // Form will submit normally
});
</script>
@endpush
