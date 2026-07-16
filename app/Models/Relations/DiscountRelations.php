<?php

namespace App\Models\Relations;

use App\Models\DiscountUsage;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait DiscountRelations
{
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
