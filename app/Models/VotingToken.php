<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VotingToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'user_id',
        'token',
        'is_used',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateToken($electionId, $userId)
    {
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addHours(config('voting.token_expiry_hours', 24));

        return self::create([
            'election_id' => $electionId,
            'user_id' => $userId,
            'token' => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);
    }

    public function isValid()
    {
        return !$this->is_used && Carbon::now() < $this->expires_at;
    }

    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => Carbon::now(),
        ]);
    }
}
