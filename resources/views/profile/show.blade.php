@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4 position-relative d-inline-block">
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
                        <button class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0" 
                                style="width: 40px; height: 40px;"
                                data-bs-toggle="modal" 
                                data-bs-target="#avatarModal"
                                aria-label="Change avatar">
                            <i class="bi bi-camera-fill"></i>
                        </button>
                    </div>
                    
                    <h3 class="fw-bold mb-2">{{ $user->name }}</h3>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    @if($user->bio)
                        <p class="text-muted small mb-3">{{ $user->bio }}</p>
                    @endif
                    
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
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-start">
                        <p class="mb-2"><i class="bi bi-card-checklist text-primary"></i> <strong>Voter ID:</strong> {{ $user->voter_id }}</p>
                        <p class="mb-2"><i class="bi bi-calendar-check text-primary"></i> <strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                        <p class="mb-0"><i class="bi bi-clock-history text-primary"></i> <strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'First time' }}</p>
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
                            <div class="d-flex gap-2">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle-fill"></i> Voted
                                </span>
                                <a href="{{ route('voting.results', $vote->election) }}" class="btn btn-sm btn-outline-primary">
                                    View Results
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                            <p class="text-muted mt-3">No voting history yet</p>
                            <a href="{{ route('voting.index') }}" class="btn btn-primary">
                                Browse Elections
                            </a>
                        </div>
                    @endforelse
                    
                    <div class="mt-3">
                        {{ $votingHistory->links() }}
                    </div>
                </div>
            </div>
            
            <!-- Account Settings Preview -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-gear-fill text-primary"></i> Quick Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-envelope text-primary"></i>
                                <strong>Email Notifications</strong>
                            </div>
                            <span class="badge bg-{{ $user->email_notifications ? 'success' : 'secondary' }}">
                                {{ $user->email_notifications ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-phone text-primary"></i>
                                <strong>SMS Notifications</strong>
                            </div>
                            <span class="badge bg-{{ $user->sms_notifications ? 'success' : 'secondary' }}">
                                {{ $user->sms_notifications ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-sliders"></i> Manage All Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Change Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
                        <small class="text-muted">Maximum file size: 2MB. Recommended: Square image, 400x400px or larger.</small>
                    </div>
                    
                    @if($user->avatar)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You currently have an avatar. Uploading a new one will replace it.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($user->avatar)
                        <a href="{{ route('profile.avatar.delete') }}" 
                           class="btn btn-danger me-auto"
                           onclick="return confirm('Are you sure you want to remove your avatar?')">
                            <i class="bi bi-trash"></i> Remove Avatar
                        </a>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
