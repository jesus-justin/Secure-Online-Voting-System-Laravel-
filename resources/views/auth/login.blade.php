@extends('layouts.app')

@section('title', 'Login - Secure Voting')

@section('content')
<div class="min-vh-100 d-flex align-items-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
    <!-- Animated background elements -->
    <div style="position: absolute; top: -10%; left: -5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1), transparent); border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
    <div style="position: absolute; bottom: -10%; right: -5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.08), transparent); border-radius: 50%; animation: float 8s ease-in-out infinite reverse;"></div>
    
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0" style="border-radius: 20px; backdrop-filter: blur(10px);">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <div class="mb-3 position-relative">
                                <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center shadow" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-shield-check text-white" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h2 class="card-title text-dark fw-bold mb-2">Welcome Back</h2>
                            <p class="text-muted">Sign in to access your secure voting account</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong>Login Failed!</strong>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label fw-5">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-envelope text-primary"></i>
                                    </span>
                                    <input type="email" class="form-control form-control-lg border-0 @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-5">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="bi bi-lock text-primary"></i>
                                    </span>
                                    <input type="password" class="form-control form-control-lg border-0 @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Enter your password" required>
                                    <button class="btn btn-outline-secondary border-0 px-3" type="button" id="togglePassword" aria-label="Toggle password visibility">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-bold">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <p class="mb-0 text-muted">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">
                                    Create one now
                                </a>
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <p class="text-center text-muted small mb-0">
                                <i class="bi bi-shield-lock text-success"></i> 
                                Protected by military-grade encryption
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
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    // Form will submit normally
});

// Toggle password visibility
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        togglePassword.setAttribute('aria-pressed', isHidden ? 'true' : 'false');

        const icon = togglePassword.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-eye', !isHidden);
            icon.classList.toggle('bi-eye-slash', isHidden);
        }
    });
}
</script>
@endpush
