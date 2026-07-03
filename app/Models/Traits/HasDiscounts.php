<?php

namespace App\Models\Traits;

use App\Enums\DiscountType;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasDiscounts
{
    public function getIsValidAttribute(): bool
    {
        return $this->status === 'active'
            && ! $this->starts_at?->isFuture()
            && ! $this->expires_at?->isPast()
            && (! $this->max_uses || $this->used_count < $this->max_uses);
    }

    public function calculateDiscount(float $amount): float
    {
        if ($amount < $this->min_order_amount) {
            return 0;
        }

        if (! $this->meetsCondition($amount)) {
            return 0;
        }

        $discount = match ($this->type) {
            DiscountType::Percentage => $amount * ($this->value / 100),
            DiscountType::Fixed => min($this->value, $amount),
        };

        if ($this->type === DiscountType::Percentage && $this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    private function meetsCondition(float $amount): bool
    {
        if (! $this->is_condition) {
            return true;
        }

        if ($this->min_condition_value !== null && $amount < $this->min_condition_value) {
            return false;
        }

        if ($this->max_condition_value !== null && $amount > $this->max_condition_value) {
            return false;
        }

        return true;
    }

    public function recordUsage(User $user, Order $order, float $discountAmount): void
    {
        DiscountUsage::create([
            'user_id' => $user->id,
            'discount_id' => $this->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);

        $this->increment('used_count');
    }

    public function usageCountForUser(int $userId): int
    {
        return $this->usages()
            ->where('user_id', $userId)
            ->count();
    }

    public function scopeActive(Builder $query): Builder{
        return $query->where('status', 'active');
    }
    public function scopeValid(Builder $query): Builder
    {
        return $query
            ->active()
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
