@extends('layouts.app')

@section('title', $election->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-ballot"></i> {{ $election->title }}</h4>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $election->description }}</p>

                    <div class="alert alert-info">
                        <i class="bi bi-clock"></i> 
                        Voting ends on {{ $election->end_time->format('F d, Y \a\t H:i') }}
                    </div>

                    @if($hasVoted)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> You have already voted in this election.
                        </div>
                    @else
                        <form method="POST" action="{{ route('voting.vote', $election) }}" id="voteForm">
                            @csrf

                            <h5 class="mb-3">Select Your Candidate:</h5>

                            <div class="row">
                                @foreach($candidates as $candidate)
                                    <div class="col-md-6 mb-3">
                                        <div class="card candidate-card">
                                            <div class="card-body">
                                                @if($candidate->photo)
                                                    <img src="{{ asset('storage/' . $candidate->photo) }}" 
                                                         class="img-fluid mb-3 rounded" alt="{{ $candidate->name }}">
                                                @endif
                                                <h5 class="card-title">{{ $candidate->name }}</h5>
                                                <p class="card-text">{{ $candidate->description }}</p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" 
                                                           name="candidate_id" id="candidate{{ $candidate->id }}" 
                                                           value="{{ $candidate->id }}" required>
                                                    <label class="form-check-label" for="candidate{{ $candidate->id }}">
                                                        Vote for {{ $candidate->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
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
document.getElementById('voteForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    if (!confirm('Are you sure you want to cast this vote? This action cannot be undone.')) {
        return;
    }
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('recaptcha.site_key') }}', {action: 'vote'}).then(function(token) {
            document.getElementById('recaptcha_token').value = token;
            e.target.submit();
        });
    });
});
</script>
@endpush
