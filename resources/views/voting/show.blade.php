@extends('layouts.app')

@section('title', $election->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-ballot-fill me-3" style="font-size: 2.5rem;"></i>
                        <div>
                            <h3 class="mb-0">{{ $election->title }}</h3>
                            <small class="opacity-75">Cast your secure vote</small>
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
                        <form method="POST" action="{{ route('voting.vote', $election) }}" id="voteForm">
                            @csrf

                            <h5 class="mb-4 fw-bold text-center" style="font-size: 1.3rem;">
                                <i class="bi bi-person-check-fill text-primary"></i> Select Your Candidate
                            </h5>

                            <div class="row g-4">
                                @foreach($candidates as $candidate)
                                    <div class="col-md-6">
                                        <label for="candidate{{ $candidate->id }}" class="w-100">
                                            <div class="card candidate-card h-100 border-2 position-relative" 
                                                 style="cursor: pointer; transition: all 0.3s ease;">
                                                <div class="card-body p-4">
                                                    @if($candidate->photo)
                                                        <div class="text-center mb-3">
                                                            <img src="{{ asset('storage/' . $candidate->photo) }}" 
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

                            <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send-check"></i> Cast Your Vote
                                </button>
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
    voteForm.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to cast this vote? This action cannot be undone.')) {
            e.preventDefault();
        }
        // Form will submit normally - recaptcha disabled for now
    });
}
</script>
@endpush
