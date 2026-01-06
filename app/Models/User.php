<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'voter_id',
        'is_admin',
        'is_verified',
        'verified_at',
        'last_login_at',
        'email_notifications',
        'sms_notifications',
        'phone_number',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

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

    public function hasVotedInElection($electionId)
    {
        return $this->votes()->where('election_id', $electionId)->exists();
    }

    public function canVoteInElection($electionId)
    {
        if (!$this->is_verified) {
            return false;
        }

        return !$this->hasVotedInElection($electionId);
    }
}
