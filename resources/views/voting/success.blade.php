@extends('layouts.app')

@section('title', 'Vote Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 text-center" style="border-radius: 20px;">
                <div class="card-body p-5">
                    <div class="mb-4 success-icon">
                        <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" 
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-check-circle-fill text-white" style="font-size: 5rem;"></i>
                        </div>
                    </div>
                    
                    <h1 class="text-success fw-bold mb-3">Vote Recorded Successfully!</h1>
                    <p class="lead text-muted mb-4">
                        Your vote for <strong class="text-primary">{{ $election->title }}</strong> has been securely recorded and encrypted.
                    </p>

                    <div class="alert alert-success border-0 shadow-sm mt-4 mb-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-shield-check-fill me-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Your vote is:</strong><br>
                                <small>Encrypted • Anonymous • Immutable</small>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-clock-fill text-primary"></i> 
                                <strong>Timestamp:</strong> {{ now()->format('F d, Y \a\t H:i:s') }}
                            </p>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('voting.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Elections
                        </a>
                        @if($election->hasEnded() || auth()->user()->is_admin)
                            <a href="{{ route('voting.results', $election) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-bar-chart"></i> View Results
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
