<?php

namespace App\Services\Security;

use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SecurityLogService
{
    public function getLogsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = SecurityLog::with('user');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (!empty($filters['severity']) && $filters['severity'] !== 'all') {
            $query->where('severity', $filters['severity']);
        }

        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 20);
    }

    public function logEvent(string $eventType, ?User $user = null, string $severity = 'info', array $details = []): SecurityLog
    {
        $user = $user ?? auth()->user();
        $companyId = $user?->company_id;

        return SecurityLog::create([
            'company_id' => $companyId,
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent(),
            'severity' => $severity,
            'details' => $details,
        ]);
    }

    public function logLoginAttempt(string $email, bool $successful, ?string $reason = null): SecurityLog
    {
        $user = User::where('email', $email)->first();

        return SecurityLog::create([
            'company_id' => $user?->company_id,
            'user_id' => $user?->id,
            'event_type' => $successful ? 'login_success' : 'login_failed',
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'user_agent' => request()->userAgent(),
            'severity' => $successful ? 'info' : 'warning',
            'details' => [
                'email' => $email,
                'status' => $successful ? 'authenticated' : 'denied',
                'reason' => $reason,
            ],
        ]);
    }
}
