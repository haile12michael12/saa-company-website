<?php

namespace App\Models\Traits;

use App\Models\Company;

trait BelongsToCompany
{
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
