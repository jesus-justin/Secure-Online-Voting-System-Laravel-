@extends('layouts.app')

@section('title', $election->title)

@section('content')
<div class="voting-hero mb-4">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <p class="text-uppercase small fw-bold text-muted mb-1">Election</p>
                <h1 class="display-3 fw-bold mb-3">{{ $election->title }}</h1>
                <p class="lead text-muted">Cast your secure, anonymous vote with confidence</p>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <p class="text-uppercase small text-muted mb-2">Live Vote Count</p>
                        <h2 id="liveVoteCount" class="display-4 fw-bold text-primary mb-0">{{ $totalVotes ?? 0 }}</h2>
                        <div class="mt-2">
                            <span class="badge bg-success-subtle text-success pulse">
                                <i class="bi bi-broadcast"></i> Live
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-ballot-fill me-3" style="font-size: 2.5rem;"></i>
                        <div>
                            <h3 class="mb-0">Select Your Candidate</h3>
                            <small class="opacity-75">Every vote is encrypted and verified</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle-fill me-3 mt-1" style="font-size: 1.5rem;"></i>
                            <div>
                                <p class="mb-2 fw-bold">{{ $election->description }}</p>
                                <p class="mb-0 small">
                                    <i class="bi bi-clock-history"></i> 
                                    Voting ends on {{ $election->end_date->format('F d, Y \a\t H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($hasVoted)
                        <div class="alert alert-success border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-3" style="font-size: 3rem;"></i>
                                <div>
                                    <h4 class="alert-heading mb-1">Vote Already Submitted!</h4>
                                    <p class="mb-0">You have successfully cast your vote in this election. Thank you for participating!</p>
                                </div>
                            </div>
                            <hr class="my-3">
                            <a href="{{ route('voting.index') }}" class="btn btn-success">
                                <i class="bi bi-arrow-left"></i> View Other Elections
                            </a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('voting.vote', $election) }}" id="voteForm" 
                              aria-label="Vote submission form" novalidate>
                            @csrf

                            <h5 class="mb-4 fw-bold text-center" style="font-size: 1.3rem;">
                                <i class="bi bi-person-check-fill text-primary" aria-hidden="true"></i> 
                                <span>Select Your Candidate</span>
                            </h5>
                            
                            <div role="radiogroup" aria-label="Candidate selection" aria-required="true">

                            <div class="row g-4">
                                @foreach($candidates as $candidate)
                                    <div class="col-md-6">
                                        <label for="candidate{{ $candidate->id }}" class="w-100">
                                            <div class="card candidate-card h-100 border-2 position-relative" 
                                                 style="cursor: pointer; transition: all 0.3s ease;">
                                                <div class="card-body p-4">
                                                    @if($candidate->image_url)
                                                        <div class="text-center mb-3">
                                                            <img src="{{ asset('storage/' . $candidate->image_url) }}" 
                                                                 class="img-fluid rounded-circle shadow" 
                                                                 alt="{{ $candidate->name }}"
                                                                 style="width: 120px; height: 120px; object-fit: cover;">
                                                        </div>
                                                    @else
                                                        <div class="text-center mb-3">
                                                            <div class="rounded-circle bg-primary bg-gradient d-inline-flex align-items-center justify-content-center shadow" 
                                                                 style="width: 120px; height: 120px;">
                                                                <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <h5 class="card-title text-center mb-3 fw-bold">{{ $candidate->name }}</h5>
                                                    <p class="card-text text-muted text-center small">{{ $candidate->description }}</p>
                                                    
                                                    <div class="form-check text-center mt-3">
                                                        <input class="form-check-input" type="radio" 
                                                               name="candidate_id" id="candidate{{ $candidate->id }}" 
                                                               value="{{ $candidate->id }}" required
                                                               style="width: 20px; height: 20px;">
                                                        <label class="form-check-label ms-2 fw-bold text-primary" for="candidate{{ $candidate->id }}">
                                                            Select {{ $candidate->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            </div>

                            <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                            
                            <div id="candidateError" class="invalid-feedback d-block text-center" style="display: none !important;" role="alert">
                                Please select a candidate before submitting your vote.
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg py-3 shadow" 
                                        aria-label="Cast your vote securely">
                                    <i class="bi bi-send-check-fill" aria-hidden="true"></i> 
                                    <span>Cast Your Vote Securely</span>
                                </button>
                                <a href="{{ route('voting.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Elections
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const voteForm = document.getElementById('voteForm');
if (voteForm) {
    // Add change event listeners to candidate cards
    const candidateCards = voteForm.querySelectorAll('.candidate-card');
    candidateCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                // Remove error state when candidate is selected
                const errorDiv = document.getElementById('candidateError');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            }
        });
        
        // Add keyboard support
        card.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    radio.focus();
                }
            }
        });
        
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
    });
    
    voteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if a candidate is selected
        const selectedCandidate = this.querySelector('input[name="candidate_id"]:checked');
        const errorDiv = document.getElementById('candidateError');
        
        if (!selectedCandidate) {
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            showToast('Please select a candidate before submitting your vote.', 'warning');
            return;
        }
        
        // Confirm vote
        if (confirm('Are you sure you want to cast this vote? This action cannot be undone.')) {
            // Show loading state
            setFormLoading(this, true);
            
            // Get selected candidate name
            const candidateName = selectedCandidate.closest('.candidate-card').querySelector('.card-title').textContent;
            
            // Show confirmation toast
            showToast('Submitting your vote for ' + candidateName + '...', 'info');
            
            // Submit the form
            this.submit();
        }
    });
}

// Live vote counter update
const electionId = {{ $election->id }};
function updateLiveVoteCount() {
    fetch(`/api/elections/${electionId}/vote-count`)
        .then(response => response.json())
        .then(data => {
            const counter = document.getElementById('liveVoteCount');
            if (counter && data.count !== undefined) {
                const currentCount = parseInt(counter.textContent);
                const newCount = data.count;
                
                if (newCount !== currentCount) {
                    // Animate counter update
                    animateCount(counter, currentCount, newCount, 500);
                }
            }
        })
        .catch(error => console.error('Error fetching vote count:', error));
}

function animateCount(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Update every 10 seconds
setInterval(updateLiveVoteCount, 10000);
</script>
@endpush
