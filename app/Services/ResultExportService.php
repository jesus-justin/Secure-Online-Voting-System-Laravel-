<?php

namespace App\Services;

use App\Models\Election;
use Illuminate\Support\Facades\View;

class ResultExportService
{
    /**
     * Generate PDF export data for election results
     */
    public function generateResultsData(Election $election): array
    {
        $results = $election->candidates()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        $totalVotes = $election->votes()->count();

        return [
            'election' => [
                'title' => $election->title,
                'description' => $election->description,
                'start_date' => $election->start_date?->format('F d, Y'),
                'end_date' => $election->end_date?->format('F d, Y'),
                'total_votes' => $totalVotes,
            ],
            'results' => $results->map(fn($candidate, $index) => [
                'rank' => $index + 1,
                'name' => $candidate->name,
                'votes' => $candidate->votes_count,
                'percentage' => $totalVotes > 0 
                    ? round(($candidate->votes_count / $totalVotes) * 100, 2)
                    : 0,
            ])->toArray(),
            'generated_at' => now()->format('F d, Y \a\t H:i:s'),
        ];
    }

    /**
     * Get HTML view for PDF generation
     */
    public function getResultsHtml(Election $election): string
    {
        $data = $this->generateResultsData($election);
        
        return View::make('exports.election-results', $data)->render();
    }

    /**
     * Export to CSV format
     */
    public function exportToCSV(Election $election): string
    {
        $data = $this->generateResultsData($election);
        
        $csv = "Election Results: {$data['election']['title']}\n";
        $csv .= "Generated: {$data['generated_at']}\n\n";
        $csv .= "Rank,Candidate,Votes,Percentage\n";
        
        foreach ($data['results'] as $result) {
            $csv .= "{$result['rank']},{$result['name']},{$result['votes']},{$result['percentage']}%\n";
        }
        
        return $csv;
    }

    /**
     * Export to JSON format
     */
    public function exportToJSON(Election $election): string
    {
        $data = $this->generateResultsData($election);
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Export detailed analytics
     */
    public function exportDetailedAnalytics(Election $election): array
    {
        $data = $this->generateResultsData($election);
        
        // Add vote distribution over time
        $votesByDate = $election->votes()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Add hourly distribution
        $votesByHour = $election->votes()
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        $data['analytics'] = [
            'votes_by_date' => $votesByDate,
            'votes_by_hour' => $votesByHour,
            'average_votes_per_candidate' => count($data['results']) > 0
                ? round($data['election']['total_votes'] / count($data['results']), 2)
                : 0,
        ];

        return $data;
    }
}
