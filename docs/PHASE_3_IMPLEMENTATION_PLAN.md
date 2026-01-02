# Phase 3 Implementation Plan - Nice-to-Have Features

**Timeline**: Week 5+  
**Status**: Ready for Implementation  
**Last Updated**: January 2, 2026

---

## 📋 Overview

This document provides detailed implementation instructions for Phase 3 enhancements to the Secure Online Voting System. These features improve user experience and add modern functionality while maintaining all existing features.

---

## 🌙 1. Dark Mode Support

### Objective
Implement a theme toggle allowing users to switch between light and dark modes with preferences saved locally.

### Technical Approach

#### 1.1 CSS Variables Setup
**File**: `public/css/custom.css`

```css
/* Theme Variables */
:root {
    /* Light Mode (Default) */
    --bg-primary: #ffffff;
    --bg-secondary: #f8f9fa;
    --text-primary: #1a1a1a;
    --text-secondary: #6c757d;
    --border-color: #dee2e6;
    --card-bg: #ffffff;
    --card-shadow: rgba(0, 0, 0, 0.08);
    
    /* Gradients */
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --gradient-danger: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

[data-theme="dark"] {
    /* Dark Mode */
    --bg-primary: #1a1a1a;
    --bg-secondary: #2d2d2d;
    --text-primary: #ffffff;
    --text-secondary: #b0b0b0;
    --border-color: #404040;
    --card-bg: #2d2d2d;
    --card-shadow: rgba(0, 0, 0, 0.3);
    
    /* Gradients remain the same or adjust slightly */
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

body {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.card {
    background-color: var(--card-bg);
    color: var(--text-primary);
    box-shadow: 0 2px 10px var(--card-shadow);
}

.text-muted {
    color: var(--text-secondary) !important;
}

.border {
    border-color: var(--border-color) !important;
}
```

#### 1.2 Theme Toggle Component
**File**: `resources/views/layouts/app.blade.php`

Add to navbar (after existing nav items):

```php
<!-- Theme Toggle -->
<li class="nav-item">
    <button class="nav-link btn btn-link px-3" id="themeToggle" aria-label="Toggle theme">
        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        <span class="d-none d-md-inline ms-1" id="themeText">Dark</span>
    </button>
</li>
```

#### 1.3 JavaScript Implementation
**File**: `resources/views/layouts/app.blade.php` (before closing `</body>`)

```html
<script>
// Theme Management
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
        });
    }
    
    function updateThemeUI(theme) {
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill';
            if (themeText) themeText.textContent = 'Light';
        } else {
            themeIcon.className = 'bi bi-moon-stars-fill';
            if (themeText) themeText.textContent = 'Dark';
        }
    }
})();
</script>
```

#### 1.4 Implementation Steps
1. Add CSS variables to `custom.css`
2. Update all existing color references to use variables
3. Add theme toggle button to navbar
4. Implement JavaScript for theme switching
5. Test all pages in both themes
6. Ensure gradients look good in both modes

#### 1.5 Testing Checklist
- [ ] Theme persists across page reloads
- [ ] All text is readable in both modes
- [ ] Cards and borders visible in dark mode
- [ ] Gradients look good in both themes
- [ ] Transition is smooth
- [ ] Mobile layout works correctly

---

## 👤 2. User Profile & Settings Page

### Objective
Create a comprehensive user profile page where users can view voting history, manage settings, and update their account.

### Technical Approach

#### 2.1 Database Migration
**File**: Create new migration

```bash
php artisan make:migration add_profile_settings_to_users_table
```

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('email_notifications')->default(true);
        $table->boolean('sms_notifications')->default(false);
        $table->string('phone_number')->nullable();
        $table->string('avatar')->nullable();
        $table->text('bio')->nullable();
    });
}
```

#### 2.2 Controller
**File**: `app/Http/Controllers/ProfileController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $votingHistory = $user->votes()
            ->with('election')
            ->latest()
            ->paginate(10);
            
        return view('profile.show', compact('user', 'votingHistory'));
    }
    
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }
    
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        auth()->user()->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return back()->with('success', 'Password updated successfully!');
    }
    
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048'
        ]);
        
        $path = $request->file('avatar')->store('avatars', 'public');
        
        auth()->user()->update(['avatar' => $path]);
        
        return back()->with('success', 'Avatar updated successfully!');
    }
}
```

#### 2.3 Routes
**File**: `routes/web.php`

```php
// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});
```

#### 2.4 View - Profile Page
**File**: `resources/views/profile/show.blade.php`

```php
@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 class="rounded-circle shadow-lg" 
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 alt="{{ $user->name }}">
                        @else
                            <div class="rounded-circle bg-primary bg-gradient d-inline-flex align-items-center justify-content-center shadow-lg" 
                                 style="width: 150px; height: 150px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 5rem;"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h3 class="fw-bold mb-2">{{ $user->name }}</h3>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="mb-4">
                        @if($user->verified_at)
                            <span class="badge bg-success bg-gradient px-3 py-2">
                                <i class="bi bi-check-circle-fill"></i> Verified
                            </span>
                        @else
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </span>
                        @endif
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="bi bi-pencil-fill"></i> Edit Profile
                        </a>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="bi bi-image"></i> Change Avatar
                        </button>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <p class="mb-2"><i class="bi bi-calendar-check text-primary"></i> <strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                        <p class="mb-2"><i class="bi bi-clock-history text-primary"></i> <strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'N/A' }}</p>
                        <p class="mb-0"><i class="bi bi-card-checklist text-primary"></i> <strong>Voter ID:</strong> {{ $user->voter_id }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Voting History -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history text-primary"></i> Voting History
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($votingHistory as $vote)
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $vote->election->title }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ $vote->created_at->format('M d, Y \a\t H:i A') }}
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill"></i> Voted
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                            <p class="text-muted mt-3">No voting history yet</p>
                        </div>
                    @endforelse
                    
                    <div class="mt-3">
                        {{ $votingHistory->links() }}
                    </div>
                </div>
            </div>
            
            <!-- Account Settings -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-gear-fill text-primary"></i> Account Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-circle text-primary"></i>
                                <strong>Personal Information</strong>
                                <p class="mb-0 small text-muted">Update your name, email, and bio</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="#passwordSection" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-shield-lock text-primary"></i>
                                <strong>Security & Password</strong>
                                <p class="mb-0 small text-muted">Change your password</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="#notificationSection" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-bell text-primary"></i>
                                <strong>Notifications</strong>
                                <p class="mb-0 small text-muted">Manage email and SMS preferences</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
                        <small class="text-muted">Maximum file size: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

#### 2.5 View - Edit Profile
**File**: `resources/views/profile/edit.blade.php`

```php
@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square text-primary"></i> Edit Profile
                    </h4>
                </div>
                <div class="card-body p-4">
                    <!-- Personal Information -->
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <h5 class="fw-bold mb-4">Personal Information</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label fw-bold">Phone Number (Optional)</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" 
                                   value="{{ old('phone_number', $user->phone_number) }}">
                        </div>
                        
                        <div class="mb-4">
                            <label for="bio" class="form-label fw-bold">Bio (Optional)</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3" 
                                      maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                            <small class="text-muted">Maximum 500 characters</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="fw-bold mb-4">Notification Preferences</h5>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_notifications" 
                                   name="email_notifications" value="1" 
                                   {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Email Notifications</strong><br>
                                <small class="text-muted">Receive updates about new elections and results</small>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                   name="sms_notifications" value="1"
                                   {{ old('sms_notifications', $user->sms_notifications) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_notifications">
                                <strong>SMS Notifications</strong><br>
                                <small class="text-muted">Receive text messages for important updates</small>
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
                    <form method="POST" action="{{ route('profile.password') }}" id="passwordSection">
                        @csrf
                        @method('PUT')
                        
                        <h5 class="fw-bold mb-4">Change Password</h5>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
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
@endsection
```

#### 2.6 Update Navigation
Add to navbar in `layouts/app.blade.php`:

```php
<li class="nav-item">
    <a class="nav-link px-3" href="{{ route('profile.show') }}">
        <i class="bi bi-person-circle me-1"></i> 
        <span class="d-none d-md-inline">Profile</span>
    </a>
</li>
```

#### 2.7 Implementation Steps
1. Run migration for profile fields
2. Create ProfileController
3. Add routes
4. Create profile views
5. Update User model relationships
6. Add profile link to navigation
7. Test all profile features

---

## 📊 3. Admin Dashboard Enhancements

### Objective
Add comprehensive analytics, charts, and insights to the admin dashboard.

### Technical Approach

#### 3.1 Install Chart.js
```bash
npm install chart.js
```

Or use CDN in layout:
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

#### 3.2 Controller Updates
**File**: `app/Http/Controllers/AdminController.php`

```php
public function dashboard()
{
    // Existing stats
    $totalElections = Election::count();
    $activeElections = Election::where('status', 'active')->count();
    $totalVotes = Vote::count();
    $totalUsers = User::count();
    $pendingVerifications = User::whereNull('verified_at')->count();
    $recentElections = Election::latest()->take(5)->get();
    
    // NEW: Analytics data
    $votesOverTime = Vote::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    $participationRate = $this->calculateParticipationRate();
    $peakVotingHours = $this->getPeakVotingHours();
    $recentActivity = $this->getRecentActivity();
    $systemHealth = $this->getSystemHealth();
    
    return view('admin.dashboard', compact(
        'totalElections',
        'activeElections',
        'totalVotes',
        'totalUsers',
        'pendingVerifications',
        'recentElections',
        'votesOverTime',
        'participationRate',
        'peakVotingHours',
        'recentActivity',
        'systemHealth'
    ));
}

private function calculateParticipationRate()
{
    $activeElections = Election::where('status', 'active')->get();
    $rates = [];
    
    foreach ($activeElections as $election) {
        $eligibleVoters = User::whereNotNull('verified_at')->count();
        $actualVotes = $election->votes()->count();
        $rate = $eligibleVoters > 0 ? ($actualVotes / $eligibleVoters) * 100 : 0;
        
        $rates[] = [
            'election' => $election->title,
            'rate' => round($rate, 2),
            'votes' => $actualVotes,
            'eligible' => $eligibleVoters
        ];
    }
    
    return $rates;
}

private function getPeakVotingHours()
{
    return Vote::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
        ->groupBy('hour')
        ->orderBy('count', 'desc')
        ->take(5)
        ->get();
}

private function getRecentActivity()
{
    return VoteLog::with('user', 'election')
        ->latest()
        ->take(10)
        ->get();
}

private function getSystemHealth()
{
    return [
        'database' => $this->checkDatabaseConnection(),
        'queue' => $this->checkQueueHealth(),
        'storage' => $this->checkStorageSpace(),
        'cache' => $this->checkCacheConnection()
    ];
}

private function checkDatabaseConnection()
{
    try {
        DB::connection()->getPdo();
        return ['status' => 'healthy', 'message' => 'Connected'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
```

#### 3.3 Enhanced Dashboard View
**File**: `resources/views/admin/dashboard.blade.php`

Add after statistics cards:

```php
<!-- Analytics Section -->
<div class="row mb-5">
    <!-- Votes Over Time Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-graph-up text-primary"></i> Votes Over Time (Last 30 Days)
                </h5>
                <button class="btn btn-sm btn-outline-primary" onclick="exportChartData()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
            <div class="card-body">
                <canvas id="votesChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Participation Rate -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-percent text-success"></i> Participation Rate
                </h5>
            </div>
            <div class="card-body">
                @foreach($participationRate as $rate)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">{{ Str::limit($rate['election'], 20) }}</span>
                            <span class="badge bg-primary">{{ $rate['rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-gradient" 
                                 style="width: {{ $rate['rate'] }}%"
                                 role="progressbar">
                                {{ $rate['votes'] }}/{{ $rate['eligible'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Peak Voting Hours -->
<div class="row mb-5">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock text-warning"></i> Peak Voting Hours
                </h5>
            </div>
            <div class="card-body">
                <canvas id="peakHoursChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- System Health -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-heart-pulse text-danger"></i> System Health
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($systemHealth as $component => $health)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="bi bi-{{ $health['status'] === 'healthy' ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }}"></i>
                                <strong class="text-capitalize">{{ $component }}</strong>
                                <p class="mb-0 small text-muted">{{ $health['message'] }}</p>
                            </div>
                            <span class="badge bg-{{ $health['status'] === 'healthy' ? 'success' : 'danger' }}">
                                {{ $health['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Feed -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-activity text-info"></i> Recent Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($recentActivity as $activity)
                        <div class="timeline-item mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-check text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">
                                        <strong>{{ $activity->user->name }}</strong> voted in 
                                        <strong>{{ $activity->election->title }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $activity->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Votes Over Time Chart
const votesCtx = document.getElementById('votesChart').getContext('2d');
const votesChart = new Chart(votesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($votesOverTime->pluck('date')) !!},
        datasets: [{
            label: 'Votes',
            data: {!! json_encode($votesOverTime->pluck('count')) !!},
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Peak Hours Chart
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
const peakChart = new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($peakVotingHours->pluck('hour')->map(fn($h) => $h . ':00')) !!},
        datasets: [{
            label: 'Votes',
            data: {!! json_encode($peakVotingHours->pluck('count')) !!},
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: 'rgb(255, 193, 7)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function exportChartData() {
    // Implementation for exporting chart data
    const data = {!! json_encode($votesOverTime) !!};
    const csv = 'Date,Votes\n' + data.map(d => `${d.date},${d.count}`).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'votes_over_time.csv';
    a.click();
}
</script>
@endpush
```

#### 3.4 Implementation Steps
1. Install Chart.js
2. Update AdminController with analytics methods
3. Update dashboard view with charts
4. Test all charts render correctly
5. Add export functionality
6. Optimize queries for performance

---

## 📱 4. Progressive Web App (PWA)

### Objective
Make the application installable and work offline.

### Technical Approach

#### 4.1 Web App Manifest
**File**: `public/manifest.json`

```json
{
  "name": "Secure Online Voting System",
  "short_name": "SecureVote",
  "description": "Cast your vote securely and anonymously",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#667eea",
  "theme_color": "#667eea",
  "orientation": "portrait",
  "icons": [
    {
      "src": "/images/icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png"
    },
    {
      "src": "/images/icon-96x96.png",
      "sizes": "96x96",
      "type": "image/png"
    },
    {
      "src": "/images/icon-128x128.png",
      "sizes": "128x128",
      "type": "image/png"
    },
    {
      "src": "/images/icon-144x144.png",
      "sizes": "144x144",
      "type": "image/png"
    },
    {
      "src": "/images/icon-152x152.png",
      "sizes": "152x152",
      "type": "image/png"
    },
    {
      "src": "/images/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/images/icon-384x384.png",
      "sizes": "384x384",
      "type": "image/png"
    },
    {
      "src": "/images/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

#### 4.2 Service Worker
**File**: `public/sw.js`

```javascript
const CACHE_NAME = 'secure-vote-v1';
const urlsToCache = [
    '/',
    '/css/custom.css',
    '/js/app.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'
];

// Install event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Fetch event
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            })
            .catch(() => {
                // Return offline page if both fail
                return caches.match('/offline.html');
            })
    );
});

// Activate event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});
```

#### 4.3 Register Service Worker
**File**: `resources/views/layouts/app.blade.php`

Add before closing `</body>`:

```html
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('Service Worker registered:', registration);
            })
            .catch(error => {
                console.log('Service Worker registration failed:', error);
            });
    });
}
</script>
```

#### 4.4 Add Manifest to Layout
**File**: `resources/views/layouts/app.blade.php`

Add to `<head>`:

```html
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#667eea">
<link rel="apple-touch-icon" href="/images/icon-192x192.png">
```

#### 4.5 Offline Page
**File**: `public/offline.html`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Secure Voting</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 20px;
        }
        .offline-content {
            max-width: 400px;
        }
        h1 { font-size: 3rem; margin-bottom: 1rem; }
        p { font-size: 1.2rem; opacity: 0.9; }
        .retry-btn {
            margin-top: 2rem;
            padding: 12px 30px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="offline-content">
        <h1>📡</h1>
        <h2>You're Offline</h2>
        <p>Please check your internet connection and try again.</p>
        <button class="retry-btn" onclick="window.location.reload()">
            Retry
        </button>
    </div>
</body>
</html>
```

#### 4.6 Install Prompt
Add to main layout JavaScript:

```javascript
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Show install button
    const installBtn = document.getElementById('installBtn');
    if (installBtn) {
        installBtn.style.display = 'block';
        installBtn.addEventListener('click', () => {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the install prompt');
                }
                deferredPrompt = null;
                installBtn.style.display = 'none';
            });
        });
    }
});
```

---

## 📋 Implementation Checklist

### Dark Mode
- [ ] Add CSS variables to custom.css
- [ ] Update all color references
- [ ] Add theme toggle button
- [ ] Implement JavaScript for switching
- [ ] Test all pages in both themes
- [ ] Verify localStorage persistence

### User Profile
- [ ] Create and run migration
- [ ] Create ProfileController
- [ ] Add routes
- [ ] Create show.blade.php view
- [ ] Create edit.blade.php view
- [ ] Add profile link to navigation
- [ ] Test avatar upload
- [ ] Test password change
- [ ] Test notification preferences

### Admin Dashboard
- [ ] Install Chart.js
- [ ] Update AdminController
- [ ] Add analytics methods
- [ ] Create chart views
- [ ] Test all charts
- [ ] Add export functionality
- [ ] Optimize database queries
- [ ] Test system health checks

### PWA
- [ ] Create manifest.json
- [ ] Create service worker
- [ ] Generate app icons
- [ ] Create offline page
- [ ] Register service worker
- [ ] Add manifest to layout
- [ ] Test installation
- [ ] Test offline functionality

---

## 🧪 Testing Requirements

### Manual Testing
- [ ] Test dark mode on all pages
- [ ] Test profile CRUD operations
- [ ] Test charts with different data sets
- [ ] Test PWA installation on mobile
- [ ] Test offline functionality
- [ ] Verify all features work on mobile

### Browser Testing
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Mobile browsers

### Performance Testing
- [ ] Lighthouse score > 90
- [ ] Page load time < 3s
- [ ] Chart rendering performance
- [ ] PWA score > 80

---

## 📝 Notes

- All features are additive and won't break existing functionality
- Database migrations are reversible
- PWA features enhance but don't require installation
- Dark mode is optional and defaults to light
- Profile features are backward compatible

---

## 🎯 Success Metrics

- [ ] Dark mode adoption rate > 20%
- [ ] Profile completion rate > 60%
- [ ] PWA installation rate > 10%
- [ ] User engagement increase by 15%
- [ ] Admin dashboard usage increase by 40%

---

**End of Phase 3 Implementation Plan**
