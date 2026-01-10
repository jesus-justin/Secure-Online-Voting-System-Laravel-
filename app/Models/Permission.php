<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Permission constants
    public const MANAGE_ELECTIONS = 'manage_elections';
    public const VIEW_RESULTS = 'view_results';
    public const VOTE = 'vote';
    public const MANAGE_USERS = 'manage_users';
    public const VIEW_ANALYTICS = 'view_analytics';
    public const MANAGE_ROLES = 'manage_roles';
    public const VIEW_AUDIT_LOG = 'view_audit_log';

    /**
     * Get roles with this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
