<?php

namespace App\Models\Traits;

use App\Models\DiscountUsage;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasDiscounts
{
    public function discountUsages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
