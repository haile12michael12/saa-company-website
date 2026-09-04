<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Contract extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'customer_id',
        'quote_id',
        'number',
        'status',
        'content',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->number)) {
                $contract->number = 'CTR-'.strtoupper(Str::random(8));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(ContractSignature::class);
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed' || $this->signatures()->whereNotNull('signed_at')->exists();
    }
}
