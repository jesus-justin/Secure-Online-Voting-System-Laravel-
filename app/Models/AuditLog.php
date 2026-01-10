<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    // Action constants
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_VIEW = 'view';
    public const ACTION_VOTE = 'vote';
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get security severity level
     */
    public function getSeverityLevel(): string
    {
        return match ($this->action) {
            self::ACTION_DELETE, self::ACTION_LOGOUT => 'high',
            self::ACTION_UPDATE, self::ACTION_VOTE => 'medium',
            self::ACTION_LOGIN, self::ACTION_VIEW => 'low',
            default => 'medium',
        };
    }

    /**
     * Scope: get logs for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: filter by action type
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: filter by model type
     */
    public function scopeByModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope: recent logs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderByDesc('created_at');
    }
}
