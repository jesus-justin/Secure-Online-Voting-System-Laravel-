@extends('layouts.app')

@section('title', 'Active Elections')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-vote-fill text-primary"></i> Active Elections
            </h1>
            <p class="lead text-muted">Select an election below to cast your secure vote</p>
            <div class="d-inline-block mt-3">
                <span class="badge bg-primary bg-gradient px-4 py-2" style="font-size: 1rem;">
                    <i class="bi bi-check-circle-fill"></i> Secure & Anonymous Voting
                </span>
            </div>
        </div>
    </div>

    @if(auth()->user() && !auth()->user()->is_verified)
        <div class="alert alert-warning shadow-sm border-0 rounded-3 mx-auto" style="max-width: 800px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Account Verification Pending</h5>
                    <p class="mb-0">Your account is awaiting administrator verification. You'll receive access to vote once approved.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        @forelse($elections as $election)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 card-hover shadow">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title fw-bold mb-0">{{ $election->title }}</h5>
                            @if($election->isActive())
                                <span class="badge bg-success">Live</span>
                            @endif
                        </div>
                        
                        <p class="card-text text-muted mb-4">{{ Str::limit($election->description, 120) }}</p>

                        <div class="mb-4">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="bi bi-calendar-event me-2 text-primary"></i> 
                                <span>{{ $election->start_time ? $election->start_time->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-clock me-2 text-primary"></i> 
                                <span>Ends: {{ $election->end_time ? $election->end_time->format('M d, h:i A') : 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <span class="badge bg-info bg-gradient">
                                <i class="bi bi-people-fill"></i> {{ $election->candidates->count() }} Candidates
                            </span>
                            <span class="badge bg-secondary bg-gradient">
                                <i class="bi bi-bar-chart-fill"></i> {{ $election->total_votes }} Votes
                            </span>
                        </div>

                        @if(auth()->user()->hasVotedInElection($election->id))
                            <button class="btn btn-success w-100 mb-2" disabled>
                                <i class="bi bi-check-circle-fill"></i> Vote Submitted
                            </button>
                            <a href="{{ route('voting.success', $election) }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-receipt"></i> View Receipt
                            </a>
                        @else
                            <a href="{{ route('voting.show', $election) }}" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-hand-index-thumb"></i> Vote Now
                            </a>
                        @endif

                        @if($election->hasEnded() || auth()->user()?->is_admin)
                            <a href="{{ route('voting.results', $election) }}" class="btn btn-outline-primary w-100 mt-2">
                                <i class="bi bi-bar-chart-line"></i> View Results
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-inbox" style="font-size: 5rem; color: #ddd;"></i>
                    </div>
                    <h3 class="text-muted">No Active Elections</h3>
                    <p class="text-muted">There are currently no elections available. Please check back later.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-house"></i> Return to Dashboard
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
