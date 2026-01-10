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

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="alert alert-info d-none mb-3" role="status">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount">0</span> election(s) selected
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkPublish()">
                    <i class="bi bi-eye"></i> Publish
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="bulkArchive()">
                    <i class="bi bi-archive"></i> Archive
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                    <i class="bi bi-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                            </th>
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
                            <tr class="election-row" data-id="{{ $election->id }}">
                                <td>
                                    <input type="checkbox" class="election-checkbox" value="{{ $election->id }}" onchange="updateBulkActionBar()">
                                </td>
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
                                        <a href="{{ route('admin.elections.clone', $election) }}" 
                                           class="btn btn-sm btn-info" title="Clone election">Clone</a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteElection({{ $election->id }})">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No elections found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.election-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkActionBar();
}

function updateBulkActionBar() {
    const selected = document.querySelectorAll('.election-checkbox:checked').length;
    const bar = document.getElementById('bulkActionsBar');
    const count = document.getElementById('selectedCount');
    
    count.textContent = selected;
    bar.classList.toggle('d-none', selected === 0);
}

function bulkPublish() {
    const selected = getSelectedIds();
    if (selected.length === 0) return;
    
    fetch('{{ route("admin.elections.bulk-publish") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ ids: selected })
    }).then(() => location.reload());
}

function bulkArchive() {
    const selected = getSelectedIds();
    if (selected.length === 0) return;
    
    fetch('{{ route("admin.elections.bulk-archive") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ ids: selected })
    }).then(() => location.reload());
}

function bulkDelete() {
    if (!confirm('Delete selected elections? This cannot be undone.')) return;
    
    const selected = getSelectedIds();
    if (selected.length === 0) return;
    
    fetch('{{ route("admin.elections.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ ids: selected })
    }).then(() => location.reload());
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.election-checkbox:checked'))
        .map(cb => cb.value);
}

function clearSelection() {
    document.querySelectorAll('.election-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActionBar();
}

function deleteElection(id) {
    if (!confirm('Delete this election?')) return;
    
    fetch(`/admin/elections/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        }
    }).then(() => location.reload());
}
</script>
@endpush
@endsection
