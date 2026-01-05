<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vote_id',
        'election_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'old_value',
        'new_value',
        'performed_at',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'performed_at' => 'datetime',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction($electionId, $userId, $action, $ipAddress = null, $deviceFingerprint = null, $details = null, $voteId = null, $userAgent = null)
    {
        $payload = [];

        if (!is_null($details)) {
            $payload = is_array($details) ? $details : ['details' => $details];
        }

        if ($deviceFingerprint) {
            $payload['device_fingerprint'] = $deviceFingerprint;
        }

        return self::create([
            'vote_id' => $voteId,
            'election_id' => $electionId,
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'new_value' => $payload ?: null,
            'performed_at' => now(),
        ]);
    }
}
