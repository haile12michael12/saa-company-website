<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'name',
        'status',
        'description',
        'starts_at',
        'ends_at',
        'budget',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'budget' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
