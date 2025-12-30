<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'user_id',
        'action',
        'ip_address',
        'device_fingerprint',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logAction($electionId, $userId, $action, $ipAddress, $deviceFingerprint = null, $details = null)
    {
        return self::create([
            'election_id' => $electionId,
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $ipAddress,
            'device_fingerprint' => $deviceFingerprint,
            'details' => $details,
        ]);
    }
}
