<?php

namespace App\Models\Traits;

use App\Models\Discount;

trait HasDiscountUsage
{
    public function hasUsedDiscount(Discount $discount): bool
    {
        return $this->discountUsages()
            ->where('discount_id', $discount->id)
            ->exists();
    }

    public function discountUsageCount(Discount $discount): int
    {
        return $this->discountUsages()
            ->where('discount_id', $discount->id)
            ->count();
    }

    public function hasReachedDiscountLimit(Discount $discount): bool
    {
        return $discount->max_uses_per_user > 0
            && $this->discountUsageCount($discount) >= $discount->max_uses_per_user;
    }
}
