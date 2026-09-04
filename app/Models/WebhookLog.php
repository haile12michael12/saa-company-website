<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_id',
        'event',
        'response_status',
        'response_body',
        'status',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'response_status' => 'integer',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
