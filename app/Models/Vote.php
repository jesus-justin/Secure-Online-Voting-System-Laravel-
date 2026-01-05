<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    // votes table only stores created_at; disable updated_at management
    public const UPDATED_AT = null;

    protected $fillable = [
        'election_id',
        'candidate_id',
        'user_id',
        'vote_hash',
        'encrypted_vote',
        'ip_address',
        'device_fingerprint',
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

        if (empty($key) || strlen($key) !== 32) {
            throw new \RuntimeException('VOTE_ENCRYPTION_KEY must be a 32-character string.');
        }
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decryptVote($encryptedData)
    {
        $key = config('voting.vote_encryption_key');

        if (empty($key) || strlen($key) !== 32) {
            throw new \RuntimeException('VOTE_ENCRYPTION_KEY must be a 32-character string.');
        }
        list($encrypted, $iv) = explode('::', base64_decode($encryptedData), 2);
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
        return json_decode($decrypted, true);
    }

    public function verifyIntegrity()
    {
        try {
            $decryptedData = self::decryptVote($this->encrypted_vote);
            $timestamp = $this->created_at;

            if (!$timestamp) {
                return false;
            }

            $expectedHash = self::generateVoteHash(
                $this->election_id,
                $this->candidate_id,
                $this->user_id,
                $timestamp->timestamp
            );

            if ($this->vote_hash !== $expectedHash) {
                // Only mark as tampered when the column exists to avoid schema issues
                if (\Illuminate\Support\Facades\Schema::hasColumn('votes', 'is_tampered')) {
                    $this->update(['is_tampered' => true]);
                }
                return false;
            }

            return true;
        } catch (\Exception $e) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('votes', 'is_tampered')) {
                $this->update(['is_tampered' => true]);
            }
            return false;
        }
    }
}
