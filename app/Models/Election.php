<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'is_active',
        'allow_anonymous',
        'max_votes_per_user',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'allow_anonymous' => 'boolean',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function votingTokens()
    {
        return $this->hasMany(VotingToken::class);
    }

    public function voteLogs()
    {
        return $this->hasMany(VoteLog::class);
    }

    public function isActive()
    {
        $now = Carbon::now();
        return $this->is_active 
            && $this->start_time <= $now 
            && $this->end_time >= $now;
    }

    public function hasStarted()
    {
        return Carbon::now() >= $this->start_time;
    }

    public function hasEnded()
    {
        return Carbon::now() > $this->end_time;
    }

    public function getTotalVotesAttribute()
    {
        return $this->votes()->where('is_verified', true)->count();
    }

    public function getResults()
    {
        return $this->candidates()
            ->withCount(['votes' => function($query) {
                $query->where('is_verified', true)
                      ->where('is_tampered', false);
            }])
            ->orderBy('votes_count', 'desc')
            ->get();
    }
}
