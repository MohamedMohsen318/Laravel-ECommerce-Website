<?php

namespace App\Models\Traits;

use App\Enums\DiscountType;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasDiscounts
{
    // User Methods
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
        if (! $discount->max_uses_per_user) {
            return false;
        }

        return $this->discountUsageCount($discount) >= $discount->max_uses_per_user;
    }
    // Discount Accessors
    public function getIsValidAttribute(): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;

        return true;
    }
    public function getIsPercentageAttribute(): bool
    {
        return $this->type === DiscountType::Percentage;
    }

    public function getIsFixedAttribute(): bool
    {
        return $this->type === DiscountType::Fixed;
    }

    // Discount Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = now();
        return $query
            ->where('is_active', true)
            ->whereRaw('(starts_at IS NULL OR starts_at <= ?)', [$now])
            ->whereRaw('(expires_at IS NULL OR expires_at > ?)', [$now]);
    }

    // Discount Business Logic
    public function calculateDiscount(float $amount): float
    {
        if ($amount < (float) $this->min_order_amount) {
            return 0.0;
        }

        $discount = $this->is_percentage
            ? $this->percentageDiscount($amount)
            : $this->fixedDiscount($amount);

        return round($discount, 2);
    }

    private function percentageDiscount(float $amount): float
    {
        $discount = $amount * ($this->value / 100);

        return $this->max_discount_amount
            ? min($discount, (float) $this->max_discount_amount)
            : $discount;
    }

    private function fixedDiscount(float $amount): float
    {
        return min((float) $this->value, $amount);
    }

    public function recordUsage(User $user, Order $order, float $discountAmount): void
    {
        DiscountUsage::create([
            'user_id'         => $user->id,
            'discount_id'       => $this->id,
            'order_id'        => $order->id,
            'discount_amount' => $discountAmount,
        ]);

        $this->increment('used_count');
    }

    public function usageCountForUser(int $userId): int
    {
        return $this->usages()->where('user_id', $userId)->count();
    }
}
