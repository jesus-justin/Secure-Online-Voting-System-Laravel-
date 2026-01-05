@extends('layouts.app')

@section('title', 'Manage Elections')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-ballot-fill"></i> Manage Elections</h2>
        <a href="{{ route('admin.elections.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Election
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Candidates</th>
                            <th>Votes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($elections as $election)
                            <tr>
                                <td><strong>{{ $election->title }}</strong></td>
                                <td>{{ optional($election->start_date)->format('M d, Y H:i') }}</td>
                                <td>{{ optional($election->end_date)->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($election->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($election->hasEnded())
                                        <span class="badge bg-secondary">Ended</span>
                                    @else
                                        <span class="badge bg-warning">Upcoming</span>
                                    @endif
                                </td>
                                <td>{{ $election->candidates_count }}</td>
                                <td>{{ $election->votes_count }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.elections.edit', $election) }}" 
                                           class="btn btn-sm btn-primary">Edit</a>
                                        <a href="{{ route('admin.elections.results', $election) }}" 
                                           class="btn btn-sm btn-info">Results</a>
                                        <form action="{{ route('admin.elections.delete', $election) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this election?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No elections found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $elections->links() }}
        </div>
    </div>
</div>
@endsection
