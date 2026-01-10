<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'action_url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public const TYPE_VOTE_RECEIVED = 'vote_received';
    public const TYPE_ELECTION_STARTED = 'election_started';
    public const TYPE_ELECTION_ENDED = 'election_ended';
    public const TYPE_USER_VERIFIED = 'user_verified';
    public const TYPE_SECURITY_ALERT = 'security_alert';
    public const TYPE_SYSTEM_UPDATE = 'system_update';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Get CSS class for notification type
     */
    public function getTypeColorClass(): string
    {
        return match ($this->type) {
            self::TYPE_VOTE_RECEIVED => 'success',
            self::TYPE_ELECTION_STARTED => 'info',
            self::TYPE_ELECTION_ENDED => 'warning',
            self::TYPE_USER_VERIFIED => 'primary',
            self::TYPE_SECURITY_ALERT => 'danger',
            self::TYPE_SYSTEM_UPDATE => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get icon for notification type
     */
    public function getTypeIcon(): string
    {
        return match ($this->type) {
            self::TYPE_VOTE_RECEIVED => 'bi-check-circle',
            self::TYPE_ELECTION_STARTED => 'bi-play-circle',
            self::TYPE_ELECTION_ENDED => 'bi-stop-circle',
            self::TYPE_USER_VERIFIED => 'bi-shield-check',
            self::TYPE_SECURITY_ALERT => 'bi-exclamation-triangle',
            self::TYPE_SYSTEM_UPDATE => 'bi-arrow-repeat',
            default => 'bi-bell',
        };
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
