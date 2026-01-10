<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Election;
use App\Models\Vote;

class PerformanceOptimizationService
{
    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'voting_system:';

    /**
     * Get election results with caching
     */
    public function getElectionResults(Election $election, int $ttl = 300): array
    {
        $key = self::CACHE_PREFIX . "election:{$election->id}:results";

        return Cache::remember($key, $ttl, function () use ($election) {
            return $election->candidates()
                ->withCount('votes')
                ->orderBy('votes_count', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get total vote count with caching
     */
    public function getTotalVotes(Election $election, int $ttl = 60): int
    {
        $key = self::CACHE_PREFIX . "election:{$election->id}:total_votes";

        return Cache::remember($key, $ttl, function () use ($election) {
            return $election->votes()->count();
        });
    }

    /**
     * Invalidate election cache
     */
    public function invalidateElectionCache(Election $election): void
    {
        Cache::forget(self::CACHE_PREFIX . "election:{$election->id}:results");
        Cache::forget(self::CACHE_PREFIX . "election:{$election->id}:total_votes");
    }

    /**
     * Optimize vote query with eager loading
     */
    public function getOptimizedVotes(Election $election)
    {
        return Vote::where('election_id', $election->id)
            ->with(['user:id,name,email', 'candidate:id,name'])
            ->select('id', 'election_id', 'user_id', 'candidate_id', 'created_at')
            ->get();
    }

    /**
     * Batch update vote counts (for migration/cleanup)
     */
    public function batchUpdateVoteCounts(): int
    {
        return DB::transaction(function () {
            $updated = 0;

            Election::chunk(100, function ($elections) use (&$updated) {
                foreach ($elections as $election) {
                    $count = Vote::where('election_id', $election->id)->count();
                    $election->update(['total_votes' => $count]);
                    $updated++;
                }
            });

            return $updated;
        });
    }

    /**
     * Get dashboard stats with single query
     */
    public function getDashboardStats(): array
    {
        $key = self::CACHE_PREFIX . 'dashboard:stats';

        return Cache::remember($key, 300, function () {
            return [
                'total_elections' => Election::count(),
                'active_elections' => Election::where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->count(),
                'total_votes' => Vote::count(),
                'total_users' => DB::table('users')->count(),
                'recent_votes' => Vote::where('created_at', '>=', now()->subDay())->count(),
            ];
        });
    }

    /**
     * Warm up cache for active elections
     */
    public function warmUpCache(): void
    {
        $activeElections = Election::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        foreach ($activeElections as $election) {
            $this->getElectionResults($election);
            $this->getTotalVotes($election);
        }
    }

    /**
     * Clear all system caches
     */
    public function clearAllCaches(): void
    {
        Cache::tags(['elections', 'votes', 'users'])->flush();
    }
}
