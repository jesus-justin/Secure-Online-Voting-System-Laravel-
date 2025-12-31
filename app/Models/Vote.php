<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'candidate_id',
        'user_id',
        'vote_hash',
        'encrypted_vote',
        'ip_address',
        'device_fingerprint',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateVoteHash($electionId, $candidateId, $userId, $timestamp)
    {
        $data = implode('|', [$electionId, $candidateId, $userId, $timestamp, config('app.key')]);
        return hash('sha256', $data);
    }

    public static function encryptVote($data)
    {
        $key = config('voting.vote_encryption_key');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decryptVote($encryptedData)
    {
        $key = config('voting.vote_encryption_key');
        list($encrypted, $iv) = explode('::', base64_decode($encryptedData), 2);
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
        return json_decode($decrypted, true);
    }

    public function verifyIntegrity()
    {
        try {
            $decryptedData = self::decryptVote($this->encrypted_vote);
            $expectedHash = self::generateVoteHash(
                $this->election_id,
                $this->candidate_id,
                $this->user_id,
                $this->voted_at->timestamp
            );

            if ($this->vote_hash !== $expectedHash) {
                $this->update(['is_tampered' => true]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->update(['is_tampered' => true]);
            return false;
        }
    }
}
