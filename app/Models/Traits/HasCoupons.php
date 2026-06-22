<?php

namespace App\Models\Traits;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCoupons
{
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_usages')
            ->withPivot('discount_amount', 'order_id')
            ->withTimestamps();
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function hasUsedCoupon(Coupon $coupon): bool
    {
        return $this->couponUsages()->where('coupon_id', $coupon->id)->exists();
    }

    public function couponUsageCount(Coupon $coupon): int
    {
        return $this->couponUsages()->where('coupon_id', $coupon->id)->count();
    }

    public function hasReachedCouponLimit(Coupon $coupon): bool
    {
        if (! $coupon->max_uses_per_user) {
            return false;
        }

        return $this->couponUsageCount($coupon) >= $coupon->max_uses_per_user;
    }

    public function getIsValidAttribute(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function getIsPercentageAttribute(): bool
    {
        return $this->type === CouponType::Percentage;
    }

    public function getIsFixedAttribute(): bool
    {
        return $this->type === CouponType::Fixed;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function calculateDiscount(float $amount): float
    {
        if ($amount < (float) $this->min_order_amount) {
            return 0;
        }

        if ($this->is_percentage) {
            $discount = $amount * ((float) $this->value / 100);

            if ($this->max_discount_amount) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }

            return round($discount, 2);
        }

        return min((float) $this->value, $amount);
    }

    public function recordUsage(User $user, Order $order, float $discountAmount): void
    {
        CouponUsage::create([
            'user_id' => $user->id,
            'coupon_id' => $this->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);

        $this->increment('used_count');
    }

    public function usageCountForUser(int $userId): int
    {
        return $this->usages()->where('user_id', $userId)->count();
    }
}
