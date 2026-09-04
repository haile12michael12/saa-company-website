<?php

namespace App\Services\Security;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function getLogsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = AuditLog::with('user');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (!empty($filters['action']) && $filters['action'] !== 'all') {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', 'like', "%{$filters['auditable_type']}%");
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 20);
    }

    public function log(string $action, $auditable = null, ?User $user = null, ?array $old = null, ?array $new = null, ?string $description = null): AuditLog
    {
        $user = $user ?? auth()->user();
        $companyId = $user?->company_id;

        return AuditLog::create([
            'company_id' => $companyId,
            'user_id' => $user?->id,
            'action' => $action,
            'auditable_type' => $auditable ? (is_object($auditable) ? get_class($auditable) : (string)$auditable) : 'System',
            'auditable_id' => $auditable && is_object($auditable) && method_exists($auditable, 'getKey') ? $auditable->getKey() : null,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent(),
            'description' => $description ?? "Action '{$action}' performed.",
        ]);
    }
}
