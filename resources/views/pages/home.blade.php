@extends('layouts.app')

@section('title', 'Dashboard - Secure Voting')

@section('content')
<div class="container-fluid py-5" style="background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);">
    <!-- Welcome Header -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold text-dark mb-3">
                <i class="bi bi-house-heart-fill text-primary"></i> Welcome Back, {{ auth()->user()->name }}!
            </h1>
            <p class="lead text-muted mb-3">
                Here's your voting dashboard overview
            </p>
            <div class="d-flex flex-wrap gap-2">
                @if(auth()->user()->verified_at)
                    <span class="badge bg-success bg-gradient px-3 py-2" style="font-size: 0.9rem;">
                        <i class="bi bi-check-circle-fill"></i> Verified User
                    </span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.9rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i> Pending Verification
                    </span>
                @endif
                @php
                    $lastLogin = auth()->user()->last_login_at;
                @endphp
                <span class="badge bg-info bg-gradient px-3 py-2" style="font-size: 0.9rem;">
                    <i class="bi bi-clock-history"></i> Last login: {{ $lastLogin ? $lastLogin->diffForHumans() : 'First time' }}
                </span>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-4 mt-md-0">
            <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-lg shadow-sm" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    <!-- Warning Alert for Unverified Users -->
    @if(!auth()->user()->verified_at)
        <div class="alert alert-warning alert-dismissible fade show mb-5" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Account Not Verified!</strong> Your account is pending admin verification. You'll be able to vote once your account is verified.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Active Elections</h6>
                        <h2 class="mb-0 fw-bold">{{ $activeElections ?? 0 }}</h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-ballot-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Votes Cast</h6>
                        <h2 class="mb-0 fw-bold">{{ $castVotes ?? 0 }}</h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-info text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Completed Elections</h6>
                        <h2 class="mb-0 fw-bold">{{ $completedElections ?? 0 }}</h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Upcoming Elections</h6>
                        <h2 class="mb-0 fw-bold">{{ $upcomingElections ?? 0 }}</h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Active Elections -->
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Available Elections</h5>
                </div>
                <div class="card-body p-0">
                    @forelse(($elections ?? []) as $election)
                        @if($election->status == 'active')
                            <div class="p-4 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1 fw-bold">{{ $election->title }}</h6>
                                        <p class="text-muted small mb-2">{{ $election->description }}</p>
                                        <div class="d-flex gap-3">
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                            <span class="text-muted small">
                                                <i class="bi bi-people"></i> {{ $election->candidates->count() }} Candidates
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-graph-up"></i> {{ $election->votes->count() }} Votes
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        @php
                                            $hasVoted = auth()->user()->votes()->where('election_id', $election->id)->exists();
                                        @endphp
                                        @if($hasVoted)
                                            <span class="badge bg-info mb-2">✓ You have voted</span>
                                            <a href="{{ route('voting.results', $election) }}" class="btn btn-sm btn-outline-primary d-block">
                                                <i class="bi bi-eye"></i> View Results
                                            </a>
                                        @else
                                            @if(!auth()->user()->verified_at)
                                                <button class="btn btn-sm btn-secondary w-100" disabled>
                                                    <i class="bi bi-lock"></i> Pending Verification
                                                </button>
                                            @else
                                                <a href="{{ route('voting.show', $election) }}" class="btn btn-sm btn-primary w-100">
                                                    <i class="bi bi-hand-index"></i> Vote Now
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-3 mb-0">No active elections at this time.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Elections -->
            <div class="card border-0 shadow mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Upcoming Elections</h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $upcoming = ($elections ?? [])->where('status', 'pending');
                    @endphp
                    @forelse($upcoming as $election)
                        <div class="p-4 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1 fw-bold">{{ $election->title }}</h6>
                                    <p class="text-muted small mb-0">{{ $election->description }}</p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Coming Soon
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">No upcoming elections scheduled.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('voting.index') }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-ballot"></i> View All Elections
                    </a>
                    <a href="#" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#accountModal">
                        <i class="bi bi-person-gear"></i> Account Settings
                    </a>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Account Info</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6 text-muted small">Name:</dt>
                        <dd class="col-sm-6 fw-bold">{{ auth()->user()->name }}</dd>

                        <dt class="col-sm-6 text-muted small">Email:</dt>
                        <dd class="col-sm-6 fw-bold text-break">{{ auth()->user()->email }}</dd>

                        <dt class="col-sm-6 text-muted small">Status:</dt>
                        <dd class="col-sm-6">
                            @if(auth()->user()->verified_at)
                                <span class="badge bg-success"><i class="bi bi-check"></i> Verified</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6 text-muted small">Member Since:</dt>
                        <dd class="col-sm-6 small">{{ auth()->user()->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card border-0 shadow border-left-primary" style="border-left: 4px solid #0d6efd;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-shield-check text-primary"></i> Security Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Always use a strong password
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Never share your login credentials
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Verify the website URL before logging in
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Your votes are encrypted and anonymous
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success"></i> 
                            Review your voting history in audit logs
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Settings Modal -->
<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Additional account settings and options would appear here.</p>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-text"></i> View Audit Logs
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-shield"></i> Privacy Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
