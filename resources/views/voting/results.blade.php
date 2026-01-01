@extends('layouts.app')

@section('title', 'Results - ' . $election->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bar-chart-fill me-3" style="font-size: 2.5rem;"></i>
                            <div>
                                <h3 class="mb-0">Election Results</h3>
                                <p class="mb-0 opacity-75">{{ $election->title }}</p>
                            </div>
                        </div>
                        <span class="badge bg-white text-dark px-3 py-2">
                            <i class="bi bi-people-fill"></i> {{ $totalVotes }} Total Votes
                        </span>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="alert alert-info border-0 shadow-sm mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <i class="bi bi-info-circle-fill"></i> 
                                <strong>Total Votes Cast:</strong> {{ $totalVotes }}
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <small>
                                    <i class="bi bi-calendar-range"></i> 
                                    {{ $election->start_date->format('M d, Y H:i') }} - {{ $election->end_date->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($results as $index => $candidate)
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0">
                                                @if($index === 0)
                                                    <i class="bi bi-trophy-fill text-warning"></i>
                                                @endif
                                                {{ $candidate->name }}
                                            </h5>
                                            <span class="badge bg-primary">{{ $candidate->votes_count }} votes</span>
                                        </div>

                                        <div class="progress mb-2" style="height: 30px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}%"
                                                 aria-valuenow="{{ $candidate->votes_count }}" 
                                                 aria-valuemin="0" aria-valuemax="{{ $totalVotes }}">
                                                {{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}%
                                            </div>
                                        </div>

                                        @if($candidate->description)
                                            <p class="text-muted small mb-0">{{ $candidate->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('voting.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Elections
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
