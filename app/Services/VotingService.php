<?php

namespace App\Services;

use App\Models\Vote;
use App\Models\VoteLog;
use App\Models\VotingToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VotingService
{
    public function __construct(
        protected DeviceFingerprintService $deviceFingerprintService
    ) {}

    public function castVote($electionId, $candidateId, $userId, $request)
    {
        return DB::transaction(function () use ($electionId, $candidateId, $userId, $request) {
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            $deviceFingerprint = $this->deviceFingerprintService->generate($request);
            $timestamp = Carbon::now();

            // Generate vote hash
            $voteHash = Vote::generateVoteHash(
                $electionId,
                $candidateId,
                $userId,
                $timestamp->timestamp
            );

            // Encrypt vote data
            $voteData = [
                'election_id' => $electionId,
                'candidate_id' => $candidateId,
                'user_id' => $userId,
                'timestamp' => $timestamp->toDateTimeString(),
                'ip_address' => $ipAddress,
                'device_fingerprint' => $deviceFingerprint,
            ];
            $encryptedVote = Vote::encryptVote($voteData);

            // Create vote record
            $vote = Vote::create([
                'election_id' => $electionId,
                'candidate_id' => $candidateId,
                'user_id' => $userId,
                'vote_hash' => $voteHash,
                'encrypted_vote' => $encryptedVote,
                'ip_address' => $ipAddress,
                'device_fingerprint' => $deviceFingerprint,
                'user_agent' => $userAgent,
                'voted_at' => $timestamp,
                'is_verified' => true,
            ]);

            // Log the successful vote
            VoteLog::logAction(
                $electionId,
                $userId,
                'success',
                $ipAddress,
                $deviceFingerprint,
                ['vote_id' => $vote->id]
            );

            return $vote;
        });
    }

    public function validateVoteEligibility($election, $user, $request)
    {
        $errors = [];

        // Check if election is active
        if (!$election->isActive()) {
            $errors[] = 'Election is not currently active';
        }

        // Check if user has already voted
        if ($user->hasVotedInElection($election->id)) {
            $errors[] = 'You have already voted in this election';
        }

        // Check if user is verified
        if (!$user->is_verified) {
            $errors[] = 'Your account must be verified to vote';
        }

        // Check IP validation if enabled
        if (config('voting.enable_ip_validation')) {
            $ipAddress = $request->ip();
            $existingVote = Vote::where('election_id', $election->id)
                ->where('ip_address', $ipAddress)
                ->exists();

            if ($existingVote) {
                VoteLog::logAction(
                    $election->id,
                    $user->id,
                    'failed',
                    $ipAddress,
                    null,
                    ['reason' => 'IP address already voted']
                );
                $errors[] = 'A vote has already been cast from this IP address';
            }
        }

        // Check device fingerprint if enabled
        if (config('voting.enable_device_fingerprint')) {
            $deviceFingerprint = $this->deviceFingerprintService->generate($request);
            $existingVote = Vote::where('election_id', $election->id)
                ->where('device_fingerprint', $deviceFingerprint)
                ->exists();

            if ($existingVote) {
                VoteLog::logAction(
                    $election->id,
                    $user->id,
                    'failed',
                    $request->ip(),
                    $deviceFingerprint,
                    ['reason' => 'Device already voted']
                );
                $errors[] = 'A vote has already been cast from this device';
            }
        }

        return $errors;
    }

    public function detectTampering($electionId)
    {
        $votes = Vote::where('election_id', $electionId)->get();
        $tamperedCount = 0;

        foreach ($votes as $vote) {
            if (!$vote->verifyIntegrity()) {
                $tamperedCount++;
                VoteLog::logAction(
                    $electionId,
                    $vote->user_id,
                    'tampered',
                    $vote->ip_address,
                    $vote->device_fingerprint,
                    ['vote_id' => $vote->id, 'vote_hash' => $vote->vote_hash]
                );
            }
        }

        return $tamperedCount;
    }
}
