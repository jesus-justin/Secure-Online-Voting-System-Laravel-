@extends('layouts.app')

@section('title', 'Results - ' . $election->title)

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-bar-chart"></i> Election Results: {{ $election->title }}</h2>
                <a href="{{ route('admin.elections') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Elections
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-info-circle"></i> Election Summary</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Total Votes:</strong>
                            <h3 class="text-primary">{{ $totalVotes }}</h3>
                        </div>
                        <div class="col-md-3">
                            <strong>Candidates:</strong>
                            <h3 class="text-info">{{ $results->count() }}</h3>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <h3>
                                @if($election->hasEnded())
                                    <span class="badge bg-danger">Ended</span>
                                @elseif($election->hasStarted())
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Upcoming</span>
                                @endif
                            </h3>
                        </div>
                        <div class="col-md-3">
                            <strong>Election Period:</strong>
                            <small class="d-block">
                                {{ $election->start_date ? $election->start_date->format('M d, Y H:i') : 'N/A' }}
                                <br>to<br>
                                {{ $election->end_date ? $election->end_date->format('M d, Y H:i') : 'N/A' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-trophy"></i> Results</h4>
                </div>
                <div class="card-body">
                    @if($results->count() > 0)
                        <div class="row">
                            @foreach($results as $index => $candidate)
                                <div class="col-md-6 mb-4">
                                    <div class="card {{ $index === 0 ? 'border-warning' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="mb-0">
                                                    @if($index === 0)
                                                        <i class="bi bi-trophy-fill text-warning"></i>
                                                    @elseif($index === 1)
                                                        <i class="bi bi-trophy-fill text-secondary"></i>
                                                    @elseif($index === 2)
                                                        <i class="bi bi-trophy-fill" style="color: #cd7f32;"></i>
                                                    @endif
                                                    {{ $candidate->name }}
                                                </h5>
                                                <span class="badge bg-primary fs-6">{{ $candidate->votes_count }} votes</span>
                                            </div>

                                            <div class="progress mb-2" style="height: 30px;">
                                                <div class="progress-bar {{ $index === 0 ? 'bg-success' : 'bg-info' }}" role="progressbar" 
                                                     style="width: {{ $totalVotes > 0 ? ($candidate->votes_count / $totalVotes * 100) : 0 }}%"
                                                     aria-valuenow="{{ $candidate->votes_count }}" 
                                                     aria-valuemin="0" aria-valuemax="{{ $totalVotes }}">
                                                    <strong>{{ $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes * 100), 2) : 0 }}%</strong>
                                                </div>
                                            </div>

                                            @if($candidate->party)
                                                <p class="mb-1"><strong>Party:</strong> {{ $candidate->party }}</p>
                                            @endif
                                            @if($candidate->description)
                                                <p class="text-muted small mb-0">{{ Str::limit($candidate->description, 100) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No candidates found for this election.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($voteLogs->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="bi bi-clock-history"></i> Recent Vote Logs (Last 20)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($voteLogs as $log)
                                    <tr>
                                        <td>{{ $log->performed_at ? $log->performed_at->format('M d, Y H:i:s') : 'N/A' }}</td>
                                        <td>{{ $log->user ? $log->user->name : 'Unknown' }}</td>
                                        <td>
                                            <span class="badge {{ $log->action === 'success' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->ip_address }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
