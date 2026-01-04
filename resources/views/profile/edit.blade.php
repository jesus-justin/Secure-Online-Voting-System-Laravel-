@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </h4>
                </div>
                <div class="card-body p-4">
                    <!-- Personal Information -->
                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <h5 class="fw-bold mb-4 pb-2 border-bottom">
                            <i class="bi bi-person-circle text-primary"></i> Personal Information
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">Full Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label fw-bold">Phone Number (Optional)</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" name="phone_number" 
                                   value="{{ old('phone_number', $user->phone_number) }}"
                                   placeholder="+1 (555) 123-4567">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="bio" class="form-label fw-bold">Bio (Optional)</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" rows="3" 
                                      maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                            <small class="text-muted">Tell us a bit about yourself. Maximum 500 characters.</small>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="fw-bold mb-4 pb-2 border-bottom">
                            <i class="bi bi-bell text-primary"></i> Notification Preferences
                        </h5>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_notifications" 
                                   name="email_notifications" value="1" 
                                   {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Email Notifications</strong><br>
                                <small class="text-muted">Receive updates about new elections, results, and account activity</small>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                   name="sms_notifications" value="1"
                                   {{ old('sms_notifications', $user->sms_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_notifications">
                                <strong>SMS Notifications</strong><br>
                                <small class="text-muted">Receive text messages for important updates (phone number required)</small>
                            </label>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                    
                    <hr class="my-5">
                    
                    <!-- Password Change -->
                    <form method="POST" action="{{ route('profile.password') }}" id="passwordForm">
                        @csrf
                        @method('PUT')
                        
                        <h5 class="fw-bold mb-4 pb-2 border-bottom">
                            <i class="bi bi-shield-lock text-primary"></i> Change Password
                        </h5>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">Current Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">New Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Must be at least 8 characters long</small>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm New Password *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirmation')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shield-lock"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection
