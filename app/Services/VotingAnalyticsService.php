<?php

namespace App\Services;

use App\Models\Election;
use App\Models\Vote;
use Illuminate\Support\Collection;

class VotingAnalyticsService
{
    /**
     * Get vote distribution for an election
     */
    public function getVoteDistribution(Election $election): Collection
    {
        return $election->candidates()
            ->withCount('votes')
            ->get()
            ->map(fn($candidate) => [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'votes' => $candidate->votes_count,
                'percentage' => $election->total_votes > 0 
                    ? round(($candidate->votes_count / $election->total_votes) * 100, 2)
                    : 0,
            ])
            ->sortByDesc('votes');
    }

    /**
     * Calculate voting trends over time
     */
    public function getVotingTrends(Election $election, int $periods = 7): array
    {
        $votes = Vote::whereBelongsTo($election)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trends = [];
        for ($i = $periods; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trends[$date] = $votes->where('date', $date)->first()?->count ?? 0;
        }

        return $trends;
    }

    /**
     * Get voting statistics summary
     */
    public function getStatistics(Election $election): array
    {
        $totalVotes = $election->votes()->count();
        $uniqueVoters = $election->votes()->distinct('user_id')->count();
        $duplicateAttempts = $election->votes()->count() - $uniqueVoters;

        return [
            'total_votes' => $totalVotes,
            'unique_voters' => $uniqueVoters,
            'duplicate_attempts' => $duplicateAttempts,
            'participation_rate' => $election->total_participants > 0 
                ? round(($uniqueVoters / $election->total_participants) * 100, 2)
                : 0,
            'average_votes_per_candidate' => $election->candidates()->count() > 0
                ? round($totalVotes / $election->candidates()->count(), 2)
                : 0,
        ];
    }

    /**
     * Get hourly voting activity
     */
    public function getHourlyActivity(Election $election): array
    {
        $activity = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $activity[$hour] = 0;
        }

        Vote::whereBelongsTo($election)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->get()
            ->each(fn($record) => $activity[$record->hour] = $record->count);

        return $activity;
    }

    /**
     * Detect voting anomalies
     */
    public function detectAnomalies(Election $election): Collection
    {
        $anomalies = collect();
        $stats = $this->getStatistics($election);

        // High duplicate attempt rate
        if ($stats['duplicate_attempts'] > $stats['unique_voters'] * 0.1) {
            $anomalies->push([
                'type' => 'high_duplicates',
                'severity' => 'warning',
                'message' => 'High number of duplicate vote attempts detected',
                'count' => $stats['duplicate_attempts'],
            ]);
        }

        // Extremely uneven distribution
        $distribution = $this->getVoteDistribution($election);
        if ($distribution->count() > 1) {
            $leadVotes = $distribution->first()['votes'];
            $lastVotes = $distribution->last()['votes'];
            $ratio = $leadVotes > 0 ? $lastVotes / $leadVotes : 0;
            
            if ($ratio < 0.1) {
                $anomalies->push([
                    'type' => 'uneven_distribution',
                    'severity' => 'info',
                    'message' => 'Very uneven vote distribution detected',
                    'ratio' => $ratio,
                ]);
            }
        }

        return $anomalies;
    }
}
