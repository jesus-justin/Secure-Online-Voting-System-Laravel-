@extends('layouts.app')

@section('title', 'Active Elections')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-vote-fill text-primary" aria-hidden="true"></i> Active Elections
            </h1>
            <p class="lead text-muted">Select an election below to cast your secure vote</p>
            <div class="d-inline-block mt-3">
                <span class="badge bg-primary bg-gradient px-4 py-2" style="font-size: 1rem;">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Secure & Anonymous Voting
                </span>
            </div>
        </div>
    </div>
    
    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-0" 
                                       id="searchElection" 
                                       placeholder="Search elections..."
                                       aria-label="Search elections"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select border-0 bg-light" 
                                    id="filterStatus" 
                                    aria-label="Filter by status">
                                <option value="all">All Status</option>
                                <option value="active" selected>Active</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="ended">Ended</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select border-0 bg-light" 
                                    id="sortBy" 
                                    aria-label="Sort elections">
                                <option value="date">Date (Newest)</option>
                                <option value="date-old">Date (Oldest)</option>
                                <option value="name">Name (A-Z)</option>
                                <option value="votes">Most Votes</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 text-muted small" id="searchResults">
                        <i class="bi bi-info-circle"></i> <span id="resultCount"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user() && !auth()->user()->is_verified)
        <div class="alert alert-warning shadow-sm border-0 rounded-3 mx-auto" style="max-width: 800px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="alert-heading mb-1">Account Verification Pending</h5>
                    <p class="mb-0">Your account is awaiting administrator verification. You'll receive access to vote once approved.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4" id="electionsContainer">
        @forelse($elections as $election)
            <div class="col-md-6 col-lg-4 election-item" 
                 data-name="{{ strtolower($election->title) }}" 
                 data-status="{{ $election->isActive() ? 'active' : ($election->hasEnded() ? 'ended' : 'upcoming') }}"
                 data-date="{{ $election->start_time ? $election->start_time->timestamp : 0 }}"
                 data-votes="{{ $election->total_votes }}">
                <div class="card h-100 card-hover shadow">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title fw-bold mb-0">{{ $election->title }}</h5>
                            @if($election->isActive())
                                <span class="badge bg-success">Live</span>
                            @endif
                        </div>
                        
                        <p class="card-text text-muted mb-4">{{ Str::limit($election->description, 120) }}</p>

                        <div class="mb-4">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="bi bi-calendar-event me-2 text-primary" aria-hidden="true"></i> 
                                <span>{{ $election->start_time ? $election->start_time->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            @if($election->isActive() && $election->end_time)
                                <div class="alert alert-warning border-0 py-2 px-3 mb-2" style="font-size: 0.875rem;">
                                    <i class="bi bi-hourglass-split" aria-hidden="true"></i> 
                                    <strong>Ends in:</strong> 
                                    <span class="countdown" 
                                          data-end="{{ $election->end_time->toIso8601String() }}"
                                          aria-live="polite"></span>
                                </div>
                            @else
                                <div class="d-flex align-items-center text-muted small">
                                    <i class="bi bi-clock me-2 text-primary" aria-hidden="true"></i> 
                                    <span>Ends: {{ $election->end_time ? $election->end_time->format('M d, h:i A') : 'N/A' }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <span class="badge bg-info bg-gradient">
                                <i class="bi bi-people-fill"></i> {{ $election->candidates->count() }} Candidates
                            </span>
                            <span class="badge bg-secondary bg-gradient">
                                <i class="bi bi-bar-chart-fill"></i> {{ $election->total_votes }} Votes
                            </span>
                        </div>

                        @if(auth()->user()->hasVotedInElection($election->id))
                            <button class="btn btn-success w-100 mb-2" disabled>
                                <i class="bi bi-check-circle-fill"></i> Vote Submitted
                            </button>
                            <a href="{{ route('voting.success', $election) }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-receipt"></i> View Receipt
                            </a>
                        @else
                            <a href="{{ route('voting.show', $election) }}" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-hand-index-thumb"></i> Vote Now
                            </a>
                        @endif

                        @if($election->hasEnded() || auth()->user()?->is_admin)
                            <a href="{{ route('voting.results', $election) }}" class="btn btn-outline-primary w-100 mt-2">
                                <i class="bi bi-bar-chart-line"></i> View Results
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-inbox" style="font-size: 5rem; color: #ddd;"></i>
                    </div>
                    <h3 class="text-muted">No Active Elections</h3>
                    <p class="text-muted">There are currently no elections available. Please check back later.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-house"></i> Return to Dashboard
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search and Filter functionality
const searchInput = document.getElementById('searchElection');
const filterStatus = document.getElementById('filterStatus');
const sortBy = document.getElementById('sortBy');
const electionsContainer = document.getElementById('electionsContainer');
const resultCount = document.getElementById('resultCount');

function filterAndSortElections() {
    const searchTerm = searchInput.value.toLowerCase();
    const status = filterStatus.value;
    const sort = sortBy.value;
    
    const items = Array.from(document.querySelectorAll('.election-item'));
    let visibleCount = 0;
    
    // Filter items
    items.forEach(item => {
        const name = item.dataset.name;
        const itemStatus = item.dataset.status;
        
        const matchesSearch = name.includes(searchTerm);
        const matchesStatus = status === 'all' || status === itemStatus;
        
        if (matchesSearch && matchesStatus) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Sort visible items
    const visibleItems = items.filter(item => item.style.display !== 'none');
    
    visibleItems.sort((a, b) => {
        switch(sort) {
            case 'date':
                return parseInt(b.dataset.date) - parseInt(a.dataset.date);
            case 'date-old':
                return parseInt(a.dataset.date) - parseInt(b.dataset.date);
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'votes':
                return parseInt(b.dataset.votes) - parseInt(a.dataset.votes);
            default:
                return 0;
        }
    });
    
    // Reorder items in DOM
    visibleItems.forEach(item => electionsContainer.appendChild(item));
    
    // Update result count
    if (searchTerm || status !== 'all') {
        resultCount.textContent = `Showing ${visibleCount} election${visibleCount !== 1 ? 's' : ''}`;
    } else {
        resultCount.textContent = `Total: ${visibleCount} election${visibleCount !== 1 ? 's' : ''}`;
    }
}

// Event listeners
if (searchInput) {
    searchInput.addEventListener('input', filterAndSortElections);
}
if (filterStatus) {
    filterStatus.addEventListener('change', filterAndSortElections);
}
if (sortBy) {
    sortBy.addEventListener('change', filterAndSortElections);
}

// Countdown Timer
function updateCountdown(element) {
    const endDate = new Date(element.dataset.end);
    const now = new Date();
    const diff = endDate - now;
    
    if (diff <= 0) {
        element.textContent = 'Election ended';
        element.closest('.alert').classList.remove('alert-warning');
        element.closest('.alert').classList.add('alert-secondary');
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    let countdownText = '';
    
    if (days > 0) {
        countdownText = `${days}d ${hours}h ${minutes}m`;
    } else if (hours > 0) {
        countdownText = `${hours}h ${minutes}m ${seconds}s`;
        // Add urgency styling if less than 24 hours
        element.closest('.alert').classList.add('alert-danger');
        element.closest('.alert').classList.remove('alert-warning');
    } else {
        countdownText = `${minutes}m ${seconds}s`;
        element.closest('.alert').classList.add('alert-danger');
        element.closest('.alert').classList.remove('alert-warning');
    }
    
    element.textContent = countdownText;
}

// Initialize countdowns
document.querySelectorAll('.countdown').forEach(countdown => {
    updateCountdown(countdown);
    setInterval(() => updateCountdown(countdown), 1000);
});

// Initialize result count
window.addEventListener('load', filterAndSortElections);
</script>
@endpush
