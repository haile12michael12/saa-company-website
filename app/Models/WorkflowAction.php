<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'sort_order',
        'type',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
        'sort_order' => 'integer',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class);
    }
}
