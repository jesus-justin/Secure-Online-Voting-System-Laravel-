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
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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
        if ($this->start_date && $this->end_date) {
            return $this->start_date <= $now && $this->end_date >= $now;
        }

        return $this->status === 'active';
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
        return $this->votes()->count();
    }

    public function getResults()
    {
        return $this->candidates()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();
    }
}
