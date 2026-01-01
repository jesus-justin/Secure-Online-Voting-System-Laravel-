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
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            Your account is pending verification. Please wait for an administrator to verify your account before you can vote.
        </div>
    @endif

    <div class="row">
        @forelse($elections as $election)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 card-hover">
                    <div class="card-body">
                        <h5 class="card-title">{{ $election->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($election->description, 100) }}</p>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-calendar-event"></i> 
                                {{ $election->start_time ? $election->start_time->format('M d, Y H:i') : 'N/A' }} - 
                                {{ $election->end_time ? $election->end_time->format('M d, Y H:i') : 'N/A' }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-info">{{ $election->candidates->count() }} Candidates</span>
                            <span class="badge bg-secondary">{{ $election->total_votes }} Votes</span>
                        </div>

                        @if(auth()->user()->hasVotedInElection($election->id))
                            <a href="{{ route('voting.success', $election) }}" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Already Voted
                            </a>
                        @else
                            <a href="{{ route('voting.show', $election) }}" class="btn btn-primary w-100">
                                <i class="bi bi-hand-index"></i> Vote Now
                            </a>
                        @endif

                        @if($election->hasEnded() || auth()->user()?->is_admin)
                            <a href="{{ route('voting.results', $election) }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="bi bi-bar-chart"></i> View Results
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No active elections at the moment.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
