<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Secure Voting">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <title>@yield('title', 'Secure Voting System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand { 
            font-weight: 700; 
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .card-hover:hover { 
            transform: translateY(-10px) scale(1.02); 
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .vote-btn { width: 100%; }
        .badge-verified { background-color: #28a745; }
        .badge-pending { background-color: #ffc107; }
        
        .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Skip to Content for Accessibility -->
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    
    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>
    
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                <i class="bi bi-shield-check me-2" style="font-size: 1.8rem;"></i> 
                <span>Secure Voting</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('home') }}">
                                <i class="bi bi-house-fill me-1"></i> 
                                <span class="d-none d-md-inline">Home</span>
                            </a>
                        </li>
                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link px-3" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> 
                                    <span class="d-none d-md-inline">Admin Dashboard</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('voting.index') }}">
                                <i class="bi bi-ballot-fill me-1"></i> 
                                <span class="d-none d-md-inline">Elections</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            @include('components.notification-bell')
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn btn-link px-3" id="themeToggle" aria-label="Toggle theme">
                                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                                <span class="d-none d-lg-inline ms-1" id="themeText">Dark</span>
                            </button>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> 
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 10px;">
                                <li class="px-3 py-2 border-bottom">
                                    <small class="text-muted">Voter ID: {{ auth()->user()->voter_id }}</small>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.show') }}">
                                        <i class="bi bi-person-circle me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-gear me-2"></i> Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('landing') }}">
                                <i class="bi bi-house me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 btn btn-light text-primary ms-2" href="{{ route('register') }}" style="border-radius: 20px;">
                                <i class="bi bi-person-plus-fill me-1"></i> Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3 border-0 shadow-sm" role="alert" style="border-left: 4px solid #28a745 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3 border-0 shadow-sm" role="alert" style="border-left: 4px solid #dc3545 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Error!</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show m-3 border-0 shadow-sm" role="alert" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-3" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Info:</strong> {{ session('info') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <main class="py-4" id="main-content">
        @yield('content')
    </main>

    <footer class="bg-white border-top py-4 mt-5 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <h5 class="mb-2 text-primary">
                        <i class="bi bi-shield-check"></i> Secure Online Voting System
                    </h5>
                    <p class="text-muted small mb-0">
                        &copy; {{ date('Y') }} All rights reserved. | Built with security in mind.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">
                        <i class="bi bi-shield-lock text-success"></i> 
                        <strong>Secured with:</strong><br>
                        <span class="badge bg-success me-1">SHA-256 Encryption</span>
                        <span class="badge bg-info me-1">Device Fingerprinting</span>
                        <span class="badge bg-primary">reCAPTCHA</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // ===== PHASE 1: UTILITY FUNCTIONS =====
    
    // Toast Notification System
    function showToast(message, type = 'info', duration = 5000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        
        const toastId = 'toast_' + Date.now();
        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.innerHTML = `
            <div class="d-flex align-items-center p-3">
                <i class="bi ${iconMap[type]} me-2" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close ms-3" onclick="closeToast('${toastId}')" aria-label="Close"></button>
            </div>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => closeToast(toastId), duration);
    }
    
    function closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }
    }
    
    // Form Loading State
    function setFormLoading(formElement, loading = true) {
        if (!formElement) return;
        
        const submitBtn = formElement.querySelector('button[type="submit"]');
        const overlay = formElement.querySelector('.form-loading-overlay');
        
        if (loading) {
            // Disable form and show loading
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-loading');
                submitBtn.setAttribute('aria-busy', 'true');
            }
            
            // Add loading overlay if not exists
            if (!overlay && formElement.style.position !== 'relative') {
                formElement.style.position = 'relative';
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'form-loading-overlay';
                loadingDiv.innerHTML = '<div class="loading-spinner" role="status"><span class="sr-only">Loading...</span></div>';
                formElement.appendChild(loadingDiv);
            }
        } else {
            // Enable form and hide loading
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-loading');
                submitBtn.removeAttribute('aria-busy');
            }
            
            if (overlay) {
                overlay.remove();
            }
        }
    }
    
    // Real-time Form Validation
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function validateField(input) {
        const value = input.value.trim();
        const type = input.type;
        const required = input.hasAttribute('required');
        
        let isValid = true;
        let message = '';
        
        if (required && !value) {
            isValid = false;
            message = 'This field is required';
        } else if (type === 'email' && value && !validateEmail(value)) {
            isValid = false;
            message = 'Please enter a valid email address';
        } else if (input.minLength && value.length < input.minLength) {
            isValid = false;
            message = `Minimum ${input.minLength} characters required`;
        }
        
        // Update visual state
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            input.setAttribute('aria-invalid', 'false');
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            input.setAttribute('aria-invalid', 'true');
        }
        
        // Update feedback message
        let feedback = input.parentElement.querySelector('.invalid-feedback');
        if (!feedback && !isValid) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentElement.appendChild(feedback);
        }
        if (feedback) {
            feedback.textContent = message;
        }
        
        return isValid;
    }
    
    // Password Strength Checker
    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        if (strength <= 2) return { level: 'weak', width: 33, color: '#dc3545' };
        if (strength <= 4) return { level: 'medium', width: 66, color: '#ffc107' };
        return { level: 'strong', width: 100, color: '#28a745' };
    }
    
    function updatePasswordStrength(passwordInput, strengthBar) {
        const password = passwordInput.value;
        const strength = checkPasswordStrength(password);
        
        if (strengthBar) {
            strengthBar.style.width = strength.width + '%';
            strengthBar.style.backgroundColor = strength.color;
            strengthBar.className = `password-strength-bar password-strength-${strength.level}`;
        }
    }
    
    // Character Counter
    function updateCharCounter(textarea, counter, maxLength) {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${textarea.value.length}/${maxLength} characters`;
        
        if (remaining < 20) {
            counter.classList.add('danger');
            counter.classList.remove('warning');
        } else if (remaining < 50) {
            counter.classList.add('warning');
            counter.classList.remove('danger');
        } else {
            counter.classList.remove('warning', 'danger');
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add real-time validation to all forms
        document.querySelectorAll('input[type="email"], input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
                    validateField(this);
                }
            });
        });
        
        // Add loading state to all forms on submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Validate all required fields
                let isValid = true;
                this.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });
                
                if (isValid) {
                    setFormLoading(this, true);
                }
            });
        });
        
        // Password strength indicators
        document.querySelectorAll('input[type="password"][name="password"]').forEach(input => {
            const strengthContainer = document.createElement('div');
            strengthContainer.className = 'password-strength';
            const strengthBar = document.createElement('div');
            strengthBar.className = 'password-strength-bar';
            strengthContainer.appendChild(strengthBar);
            
            input.parentElement.appendChild(strengthContainer);
            
            input.addEventListener('input', function() {
                updatePasswordStrength(this, strengthBar);
            });
        });
        
        // Character counters for textareas with maxlength
        document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
            const maxLength = textarea.getAttribute('maxlength');
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            counter.setAttribute('aria-live', 'polite');
            textarea.parentElement.appendChild(counter);
            
            updateCharCounter(textarea, counter, maxLength);
            
            textarea.addEventListener('input', function() {
                updateCharCounter(this, counter, maxLength);
            });
        });
    });
    
    // Export functions globally
    window.showToast = showToast;
    window.setFormLoading = setFormLoading;
    window.validateField = validateField;
    
    // ===== DARK MODE THEME TOGGLE =====
    (function() {
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');
        const html = document.documentElement;
        
        // Get saved theme or default to light
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', currentTheme);
        updateThemeUI(currentTheme);
        
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const newTheme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeUI(newTheme);
                
                // Show toast notification
                showToast(`Switched to ${newTheme} mode`, 'success');
            });
        }
        
        function updateThemeUI(theme) {
            if (theme === 'dark') {
                if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
                if (themeText) themeText.textContent = 'Light';
            } else {
                if (themeIcon) themeIcon.className = 'bi bi-moon-stars-fill';
                if (themeText) themeText.textContent = 'Dark';
            }
        }
    })();
    
    // Service Worker Registration (PWA)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('sw.js') }}')
                .then(registration => {
                    console.log('ServiceWorker registered:', registration.scope);
                    
                    // Check for updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New service worker available
                                if (confirm('New version available! Reload to update?')) {
                                    window.location.reload();
                                }
                            }
                        });
                    });
                })
                .catch(error => {
                    console.log('ServiceWorker registration failed:', error);
                });
                
            // Handle service worker messages
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data && event.data.type === 'NOTIFICATION') {
                    showToast(event.data.message, event.data.status || 'info');
                }
            });
        });
        
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(() => {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        showToast('Notifications enabled!', 'success');
                    }
                });
            }, 5000); // Ask after 5 seconds
        }
    }
    </script>
    
    @stack('scripts')
</body>
</html>
