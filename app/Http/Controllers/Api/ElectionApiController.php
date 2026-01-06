<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Http\Request;

class ElectionApiController extends Controller
{
    /**
     * Get all active elections with candidates
     */
    public function index()
    {
        $elections = Election::with('candidates')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $elections,
            'count' => $elections->count(),
        ]);
    }

    /**
     * Get a specific election with candidates and results
     */
    public function show(Election $election)
    {
        $election->load('candidates');
        
        $results = null;
        if ($election->hasEnded() || auth()->user()?->is_admin) {
            $results = $election->getResults();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'election' => $election,
                'results' => $results,
                'total_votes' => $election->total_votes,
                'is_active' => $election->isActive(),
                'has_ended' => $election->hasEnded(),
            ],
        ]);
    }

    /**
     * Get election results
     */
    public function results(Election $election)
    {
        if (!$election->hasEnded() && !auth()->user()?->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Results will be available after the election ends',
            ], 403);
        }

        $results = $election->getResults();

        return response()->json([
            'success' => true,
            'data' => [
                'election' => $election->only(['id', 'title', 'description', 'start_date', 'end_date']),
                'results' => $results,
                'total_votes' => $election->total_votes,
            ],
        ]);
    }

    /**
     * Get election statistics
     */
    public function statistics()
    {
        $stats = [
            'total_elections' => Election::count(),
            'active_elections' => Election::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'upcoming_elections' => Election::where('start_date', '>', now())->count(),
            'completed_elections' => Election::where('end_date', '<', now())->count(),
        ];

        if (auth()->check()) {
            $stats['user_votes'] = auth()->user()->votes()->count();
            $stats['available_elections'] = Election::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->whereDoesntHave('votes', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->count();
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
