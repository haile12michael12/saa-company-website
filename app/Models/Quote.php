<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'lead_id',
        'number',
        'title',
        'status', // draft, pending_approval, approved, sent, accepted, rejected, expired
        'subtotal',
        'discount_type', // fixed or percentage
        'discount_rate',
        'discount_amount',
        'tax_rate',
        'tax',
        'total',
        'currency',
        'valid_until',
        'notes',
        'terms',
        'approved_at',
        'approved_by',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'project_id',
        'token',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'valid_until' => 'date',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->token)) {
                $quote->token = Str::random(40);
            }
            if (empty($quote->number)) {
                $quote->number = 'QT-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Check if the quote is past its valid_until date.
     */
    public function isExpired(): bool
    {
        if (!$this->valid_until) {
            return false;
        }

        return $this->valid_until->isPast() && !in_array($this->status, ['accepted']);
    }

    /**
     * Effective status taking expiration into account.
     */
    public function getEffectiveStatusAttribute(): string
    {
        if ($this->isExpired() && !in_array($this->status, ['accepted', 'rejected'])) {
            return 'expired';
        }

        return $this->status ?: 'draft';
    }

    /**
     * Recalculate totals from items, discounts, and taxes.
     */
    public function recalculateTotals(bool $save = false): void
    {
        $subtotal = $this->items()->sum('total');
        $discountAmount = 0.0;

        if ($this->discount_type === 'percentage') {
            $discountAmount = ($subtotal * ($this->discount_rate / 100));
        } elseif ($this->discount_type === 'fixed') {
            $discountAmount = (float) $this->discount_rate;
        }

        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        $taxable = max(0, $subtotal - $discountAmount);
        $taxAmount = ($taxable * ($this->tax_rate / 100));
        $total = $taxable + $taxAmount;

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax = $taxAmount;
        $this->total = $total;

        if ($save) {
            $this->save();
        }
    }

    /**
     * Recipient full name.
     */
    public function getRecipientNameAttribute(): string
    {
        return $this->customer->name ?? ($this->lead->name ?? 'Valued Client');
    }

    /**
     * Recipient email address.
     */
    public function getRecipientEmailAttribute(): ?string
    {
        return $this->customer->email ?? ($this->lead->email ?? null);
    }

    /**
     * Recipient phone number.
     */
    public function getRecipientPhoneAttribute(): ?string
    {
        return $this->customer->phone ?? ($this->lead->phone ?? null);
    }

    /**
     * Recipient organization or company.
     */
    public function getRecipientCompanyAttribute(): string
    {
        if ($this->lead && $this->lead->notes && preg_match('/Company:\s*([^\n]+)/i', $this->lead->notes, $m)) {
            return trim($m[1]);
        }

        return $this->customer->name ?? 'Client Organization';
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['draft', 'pending_approval']);
    }

    public function canBeAccepted(): bool
    {
        return in_array($this->status, ['approved', 'sent', 'pending_approval', 'draft']) && !$this->isExpired();
    }

    public function canBeConvertedToCustomer(): bool
    {
        return !empty($this->lead_id) && empty($this->customer_id);
    }

    public function canBeConvertedToProject(): bool
    {
        return empty($this->project_id) && in_array($this->status, ['accepted']);
    }
}
