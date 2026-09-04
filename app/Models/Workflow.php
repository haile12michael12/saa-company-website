<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'name',
        'trigger',
        'is_active',
        'conditions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conditions' => 'array',
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class)->orderBy('sort_order');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class)->latest();
    }
}
