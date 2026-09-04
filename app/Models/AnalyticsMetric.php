<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsMetric extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'metric_key',
        'metric_value',
        'metric_date',
        'dimension',
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'metric_date' => 'date',
        'dimension' => 'array',
    ];
}
