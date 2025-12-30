<?php

namespace App\Jobs;

use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessVote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $voteId
    ) {}

    public function handle(): void
    {
        $vote = Vote::find($this->voteId);

        if (!$vote) {
            return;
        }

        // Verify vote integrity
        if (!$vote->verifyIntegrity()) {
            VoteLog::logAction(
                $vote->election_id,
                $vote->user_id,
                'tampered',
                $vote->ip_address,
                $vote->device_fingerprint,
                [
                    'vote_id' => $vote->id,
                    'reason' => 'Failed integrity check during processing'
                ]
            );
        }

        // Additional processing logic can be added here
        // e.g., sending notifications, updating statistics, etc.
    }
}
