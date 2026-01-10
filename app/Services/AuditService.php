<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    /**
     * Log an action
     */
    public function log(
        ?User $user,
        string $action,
        string $modelType,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }

    /**
     * Get activity report for a user
     */
    public function getUserActivityReport(User $user, int $days = 30): array
    {
        $logs = AuditLog::forUser($user->id)
            ->recent($days)
            ->get();

        return [
            'total_actions' => $logs->count(),
            'logins' => $logs->where('action', AuditLog::ACTION_LOGIN)->count(),
            'votes' => $logs->where('action', AuditLog::ACTION_VOTE)->count(),
            'updates' => $logs->where('action', AuditLog::ACTION_UPDATE)->count(),
            'deletions' => $logs->where('action', AuditLog::ACTION_DELETE)->count(),
            'by_date' => $logs->groupBy(fn($log) => $log->created_at->format('Y-m-d'))
                ->map->count(),
        ];
    }

    /**
     * Get security events
     */
    public function getSecurityEvents(int $days = 7)
    {
        return AuditLog::where('created_at', '>=', now()->subDays($days))
            ->whereIn('action', [
                AuditLog::ACTION_DELETE,
                AuditLog::ACTION_LOGOUT,
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get anomalous activity
     */
    public function detectAnomalies(User $user): array
    {
        $anomalies = [];
        $recentLogs = AuditLog::forUser($user->id)->recent(1)->get();

        // Multiple logins in short time
        $loginCount = $recentLogs->where('action', AuditLog::ACTION_LOGIN)->count();
        if ($loginCount > 3) {
            $anomalies[] = [
                'type' => 'multiple_logins',
                'severity' => 'warning',
                'message' => "$loginCount logins in last 24 hours",
            ];
        }

        // Check for multiple IPs
        $ips = $recentLogs->pluck('ip_address')->unique();
        if ($ips->count() > 2) {
            $anomalies[] = [
                'type' => 'multiple_ips',
                'severity' => 'info',
                'message' => 'Accessed from ' . $ips->count() . ' different IP addresses',
            ];
        }

        return $anomalies;
    }
}
