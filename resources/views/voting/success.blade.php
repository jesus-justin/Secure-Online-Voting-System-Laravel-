@extends('layouts.app')

@section('title', 'Vote Successful')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="text-success mb-3">Vote Recorded Successfully!</h2>
                    <p class="lead">Your vote for <strong>{{ $election->title }}</strong> has been securely recorded.</p>

                    <div class="alert alert-info mt-4">
                        <i class="bi bi-shield-check"></i> Your vote is encrypted and anonymous
                    </div>

                    <p class="text-muted small">
                        Timestamp: {{ now()->format('F d, Y \a\t H:i:s') }}
                    </p>

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
