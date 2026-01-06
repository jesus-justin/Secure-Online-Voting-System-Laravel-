<?php

namespace App\Services;

use App\Models\Election;
use App\Models\User;
use Illuminate\Http\Request;

class VoteValidationService
{
    /**
     * Comprehensive vote validation
     */
    public function validateVoteRequest(Election $election, User $user, Request $request): array
    {
        $errors = [];

        // Check if election exists and is active
        if (!$election->isActive()) {
            $errors[] = 'This election is not currently active.';
        }

        // Check if user is verified
        if (!$user->is_verified) {
            $errors[] = 'Your account must be verified to vote.';
        }

        // Check if user has already voted
        if ($user->hasVotedInElection($election->id)) {
            $errors[] = 'You have already voted in this election.';
        }

        // Validate candidate
        if (!$request->has('candidate_id')) {
            $errors[] = 'Please select a candidate.';
        }

        return $errors;
    }

    /**
     * Check if user can vote in election
     */
    public function canUserVote(User $user, Election $election): bool
    {
        return $user->is_verified 
            && $election->isActive() 
            && !$user->hasVotedInElection($election->id);
    }

    /**
     * Get user's voting eligibility status
     */
    public function getVotingEligibilityStatus(User $user, Election $election): array
    {
        return [
            'can_vote' => $this->canUserVote($user, $election),
            'is_verified' => $user->is_verified,
            'is_election_active' => $election->isActive(),
            'has_voted' => $user->hasVotedInElection($election->id),
            'election_status' => [
                'title' => $election->title,
                'start_date' => $election->start_date,
                'end_date' => $election->end_date,
                'is_active' => $election->isActive(),
                'has_started' => $election->hasStarted(),
                'has_ended' => $election->hasEnded(),
            ],
        ];
    }
}
