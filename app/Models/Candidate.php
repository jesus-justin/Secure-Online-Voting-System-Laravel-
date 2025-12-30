<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'name',
        'description',
        'photo',
        'position',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCountAttribute()
    {
        return $this->votes()
            ->where('is_verified', true)
            ->where('is_tampered', false)
            ->count();
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->election->total_votes;
        if ($totalVotes == 0) {
            return 0;
        }
        return round(($this->vote_count / $totalVotes) * 100, 2);
    }
}
