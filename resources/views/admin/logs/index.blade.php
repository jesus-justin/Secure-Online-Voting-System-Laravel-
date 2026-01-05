@extends('layouts.app')

@section('title', 'Vote Logs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-clipboard-data"></i> Vote Logs</h2>
        <div>
            <form method="GET" action="{{ route('admin.logs') }}" class="d-flex align-items-center gap-2">
                <select name="election" class="form-select">
                    <option value="">All Elections</option>
                    @foreach($elections as $option)
                        <option value="{{ $option->id }}" {{ isset($election) && $election?->id === $option->id ? 'selected' : '' }}>
                            {{ $option->title }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Election</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->performed_at?->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $log->election?->title ?? 'N/A' }}</td>
                                <td>{{ $log->user?->email ?? 'System' }}</td>
                                <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                <td>
                                    @php $details = $log->new_value ?? []; @endphp
                                    @if(empty($details))
                                        <span class="text-muted">None</span>
                                    @else
                                        <pre class="mb-0 small">{{ json_encode($details, JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
