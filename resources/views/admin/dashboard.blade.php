@extends('layouts.app')

@section('title', 'Admin Dashboard')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa;">
    <div class="mb-5">
        <h1 class="display-5 fw-bold text-dark mb-2">
            <i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard
        </h1>
        <p class="text-muted lead">Manage elections, users, and monitor system activity</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Elections</h5>
                    <h2>{{ $totalElections }}</h2>
                    <small>{{ $activeElections }} Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Votes</h5>
                    <h2>{{ $totalVotes }}</h2>
                    <small>All verified votes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Registered Users</h5>
                    <h2>{{ $totalUsers }}</h2>
                    <small>{{ $pendingVerifications }} Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Verifications</h5>
                    <h2>{{ $pendingVerifications }}</h2>
                    <a href="{{ route('admin.users') }}" class="text-white">View Users →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.elections.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Election
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-info">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.logs') }}" class="btn btn-secondary">
                        <i class="bi bi-journal-text"></i> View Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Elections -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Elections</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Votes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentElections as $election)
                                    <tr>
                                        <td>{{ $election->title }}</td>
                                        <td>
                                            @if($election->isActive())
                                                <span class="badge bg-success">Active</span>
                                            @elseif($election->hasEnded())
                                                <span class="badge bg-secondary">Ended</span>
                                            @else
                                                <span class="badge bg-warning">Upcoming</span>
                                            @endif
                                        </td>
                                        <td>{{ $election->votes->count() }}</td>
                                        <td>
                                            <a href="{{ route('admin.elections.edit', $election) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="{{ route('admin.elections.results', $election) }}" class="btn btn-sm btn-info">Results</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Votes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Votes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Election</th>
                                    <th>Voter</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentVotes as $vote)
                                    <tr>
                                        <td>{{ Str::limit($vote->election->title, 20) }}</td>
                                        <td>{{ $vote->user?->voter_id ?? 'Anonymous' }}</td>
                                        <td>{{ $vote->created_at?->diffForHumans() }}</td>
                                        <td>
                                            @if($vote->is_tampered)
                                                <span class="badge bg-danger">Tampered</span>
                                            @elseif($vote->is_verified)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
