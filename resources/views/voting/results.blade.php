@extends('layouts.app')

@section('title', 'Results - ' . $election->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-bar-chart"></i> Election Results: {{ $election->title }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Total Votes: <strong>{{ $totalVotes }}</strong>
                        <br>
                        <small>Election Period: {{ $election->start_date->format('M d, Y H:i') }} - {{ $election->end_date->format('M d, Y H:i') }}</small>
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
