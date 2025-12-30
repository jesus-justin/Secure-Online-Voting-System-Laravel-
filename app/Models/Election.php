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
        'status',
        'start_date',
        'end_date',
        'is_active',
        'allow_anonymous',
        'max_votes_per_user',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'allow_anonymous' => 'boolean',
        'status' => 'string',
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
        return $this->status === 'active'
            && $this->start_date <= $now
            && $this->end_date >= $now;
    }

    public function hasStarted()
    {
        return Carbon::now() >= $this->start_date;
    }

    public function hasEnded()
    {
        return Carbon::now() > $this->end_date;
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
