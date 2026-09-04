<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAudit('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->recordAudit('updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->recordAudit('deleted', $model->getOriginal(), null);
        });
    }

    public function recordAudit(string $action, ?array $oldValues = null, ?array $newValues = null): ?AuditLog
    {
        try {
            $user = Auth::user();
            $companyId = $this->company_id ?? ($user->company_id ?? null);

            return AuditLog::create([
                'company_id' => $companyId,
                'user_id' => $user?->id,
                'action' => $action,
                'auditable_type' => static::class,
                'auditable_id' => $this->getKey(),
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent(),
                'description' => "Model ".class_basename(static::class)." #{$this->getKey()} {$action}.",
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
