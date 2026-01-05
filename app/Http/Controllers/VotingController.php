<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use App\Models\VoteLog;
use App\Services\RecaptchaService;
use App\Services\VotingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class VotingController extends Controller
{
    public function __construct(
        protected RecaptchaService $recaptchaService,
        protected VotingService $votingService
    ) {}

    public function index()
    {
        $elections = Election::with('candidates')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('voting.index', compact('elections'));
    }

    public function show(Election $election)
    {
        if (!$election->isActive()) {
            return redirect()->route('voting.index')
                ->with('error', 'This election is not currently active');
        }

        $user = Auth::user();
        $hasVoted = $user->hasVotedInElection($election->id);

        $candidates = $election->candidates;

        return view('voting.show', compact('election', 'candidates', 'hasVoted'));
    }

    public function vote(Request $request, Election $election)
    {
        $user = Auth::user();

        // Rate limiting
        $key = 'vote-attempt:' . $user->id;
        $maxAttempts = config('voting.rate_limit_per_minute', 10);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Too many attempts. Please try again in {$seconds} seconds.");
        }

        RateLimiter::hit($key, 60);

        // Validate request
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'recaptcha_token' => 'required',
        ]);

        // Verify reCAPTCHA
        if (!$this->recaptchaService->verify($request->recaptcha_token, 'vote')) {
            VoteLog::logAction(
                $election->id,
                $user->id,
                'failed',
                $request->ip(),
                null,
                ['reason' => 'reCAPTCHA verification failed'],
                null,
                $request->userAgent()
            );
            return back()->with('error', 'reCAPTCHA verification failed. Please try again.');
        }

        // Validate candidate belongs to election
        $candidate = Candidate::where('id', $request->candidate_id)
            ->where('election_id', $election->id)
            ->firstOrFail();

        // Validate vote eligibility
        $validationErrors = $this->votingService->validateVoteEligibility($election, $user, $request);
        
        if (!empty($validationErrors)) {
            return back()->with('error', implode(' ', $validationErrors));
        }

        try {
            // Cast vote
            $vote = $this->votingService->castVote(
                $election->id,
                $candidate->id,
                $user->id,
                $request
            );

            return redirect()->route('voting.success', $election)
                ->with('success', 'Your vote has been successfully recorded!');
        } catch (\Exception $e) {
            VoteLog::logAction(
                $election->id,
                $user->id,
                'failed',
                $request->ip(),
                null,
                ['reason' => $e->getMessage()],
                null,
                $request->userAgent()
            );

            return back()->with('error', 'An error occurred while recording your vote. Please try again.');
        }
    }

    public function success(Election $election)
    {
        $user = Auth::user();
        
        if (!$user->hasVotedInElection($election->id)) {
            return redirect()->route('voting.show', $election);
        }

        return view('voting.success', compact('election'));
    }

    public function results(Election $election)
    {
        // Only show results after election ends
        if (!$election->hasEnded() && !Auth::user()?->is_admin) {
            return redirect()->route('voting.index')
                ->with('error', 'Results will be available after the election ends');
        }

        $results = $election->getResults();
        $totalVotes = $election->total_votes;

        return view('voting.results', compact('election', 'results', 'totalVotes'));
    }
}
