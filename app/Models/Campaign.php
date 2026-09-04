<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'name',
        'subject',
        'type',
        'status',
        'content',
        'target_audience',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'successful_count',
        'failed_count',
        'opened_count',
        'clicked_count',
        'created_by',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
        'successful_count' => 'integer',
        'failed_count' => 'integer',
        'opened_count' => 'integer',
        'clicked_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }
}
