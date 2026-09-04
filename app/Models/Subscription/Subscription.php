<?php

namespace App\Models\Subscription;

use App\Models\Company;
use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'gateway',
        'gateway_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        if ($this->status === 'active') {
            return is_null($this->ends_at) || $this->ends_at->isFuture();
        }

        if ($this->status === 'trialing') {
            return !is_null($this->trial_ends_at) && $this->trial_ends_at->isFuture();
        }

        return false;
    }

    public function onTrial(): bool
    {
        return $this->status === 'trialing' && !is_null($this->trial_ends_at) && $this->trial_ends_at->isFuture();
    }
}
