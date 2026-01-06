<?php

namespace App\Console\Commands;

use App\Models\Election;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyVoteIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votes:verify {election?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the integrity of votes in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $electionId = $this->argument('election');

        if ($electionId) {
            $election = Election::find($electionId);
            if (!$election) {
                $this->error("Election not found.");
                return 1;
            }
            $this->info("Verifying votes for election: {$election->title}");
            $votes = Vote::where('election_id', $electionId)->get();
        } else {
            $this->info("Verifying all votes in the system...");
            $votes = Vote::all();
        }

        $total = $votes->count();
        $valid = 0;
        $invalid = 0;

        $this->info("Total votes to verify: {$total}");
        $bar = $this->output->createProgressBar($total);

        foreach ($votes as $vote) {
            if ($vote->verifyIntegrity()) {
                $valid++;
            } else {
                $invalid++;
                $this->warn("\nInvalid vote detected: ID {$vote->id}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Verification Complete:");
        $this->table(
            ['Status', 'Count', 'Percentage'],
            [
                ['Valid', $valid, $total > 0 ? round(($valid / $total) * 100, 2) . '%' : '0%'],
                ['Invalid', $invalid, $total > 0 ? round(($invalid / $total) * 100, 2) . '%' : '0%'],
                ['Total', $total, '100%'],
            ]
        );

        return $invalid > 0 ? 1 : 0;
    }
}
