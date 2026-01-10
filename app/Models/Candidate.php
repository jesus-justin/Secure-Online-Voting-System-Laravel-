<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'name',
        'description',
        'image_url',
        'avatar_path',
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
        return $this->votes()->count();
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->election->total_votes;
        if ($totalVotes == 0) {
            return 0;
        }
        return round(($this->vote_count / $totalVotes) * 100, 2);
    }

    /**
     * Get the avatar URL for the candidate
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return Storage::url($this->avatar_path);
        }

        return $this->image_url ?? asset('images/default-avatar.png');
    }

    /**
     * Upload avatar for candidate
     */
    public function uploadAvatar($file): bool
    {
        if (!$file) {
            return false;
        }

        // Delete old avatar if exists
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            Storage::disk('public')->delete($this->avatar_path);
        }

        $path = $file->store("candidates/{$this->election_id}", 'public');
        return $this->update(['avatar_path' => $path]);
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar(): void
    {
        if ($this->avatar_path) {
            Storage::disk('public')->delete($this->avatar_path);
            $this->update(['avatar_path' => null]);
        }
    }
}
