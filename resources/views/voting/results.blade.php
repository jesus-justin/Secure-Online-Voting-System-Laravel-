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

                    <div class="row g-4">
                        @foreach($results as $index => $candidate)
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 h-100" 
                                     style="border-left: 4px solid {{ $index === 0 ? '#ffc107' : '#6c757d' }} !important;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0 fw-bold">
                                                @if($index === 0)
                                                    <i class="bi bi-trophy-fill text-warning me-2" style="font-size: 1.5rem;"></i>
                                                @elseif($index === 1)
                                                    <i class="bi bi-award-fill text-secondary me-2"></i>
                                                @elseif($index === 2)
                                                    <i class="bi bi-award text-info me-2"></i>
                                                @else
                                                    <span class="badge bg-light text-dark me-2">{{ $index + 1 }}</span>
                                                @endif
                                                {{ $candidate->name }}
                                            </h5>
                                            <div class="text-end">
                                                <h4 class="mb-0 text-primary">{{ $candidate->votes_count }}</h4>
                                                <small class="text-muted">votes</small>
                                            </div>
                                        </div>

                                        <div class="progress mb-3 shadow-sm" style="height: 35px; border-radius: 10px;">
                                            <div class="progress-bar {{ $index === 0 ? 'bg-warning' : 'bg-primary' }} bg-gradient" 
                                                 role="progressbar" 
                                                 style="width: {{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}%"
                                                 aria-valuenow="{{ $candidate->votes_count }}" 
                                                 aria-valuemin="0" aria-valuemax="{{ $totalVotes }}">
                                                <strong style="font-size: 1.1rem;">
                                                    {{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}%
                                                </strong>
                                            </div>
                                        </div>

                                        @if($candidate->description)
                                            <p class="text-muted small mb-0">
                                                <i class="bi bi-info-circle"></i> {{ $candidate->description }}
                                            </p>
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
