<?php

namespace App\Models\Subscription;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_interval',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function hasFeature(string $feature): bool
    {
        if (empty($this->features)) {
            return false;
        }

        if (is_array($this->features)) {
            return in_array($feature, $this->features) || ($this->features[$feature] ?? false) === true;
        }

        return false;
    }
}
