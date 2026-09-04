<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Webhook extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'name',
        'url',
        'secret',
        'event',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($webhook) {
            if (empty($webhook->secret)) {
                $webhook->secret = Str::random(32);
            }
        });
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WebhookLog::class)->latest();
    }
}
