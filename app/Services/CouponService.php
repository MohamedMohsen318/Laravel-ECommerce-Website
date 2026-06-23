<?php

namespace App\Services;

use App\Exceptions\CouponException;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CouponService
{
    private const CURRENCY = 'EGP';

    public function validate(string $code, float $orderAmount, User $user): array
    {
        $coupon = $this->findValidCoupon($code);

        $this->assertMinOrderAmount($coupon, $orderAmount);
        $this->assertGlobalUsageLimit($coupon);
        $this->assertPerUserUsageLimit($coupon, $user);

        $discountAmount = $coupon->calculateDiscount($orderAmount);

        return [
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $orderAmount - $discountAmount),
            'message' => sprintf(
                'Coupon applied successfully. You saved %s.',
                $this->formatAmount($discountAmount)
            ),
        ];
    }

    public function apply(
        Coupon $coupon,
        Order $order,
        User $user,
        float $discountAmount
    ): void {
        DB::transaction(function () use (
            $coupon,
            $order,
            $user,
            $discountAmount
        ): void {
            $coupon->recordUsage($user, $order, $discountAmount);

            $order->update([
                'coupon_id' => $coupon->id,
                'discount_amount' => $discountAmount,
                'final_total' => max(
                    0,
                    (float) $order->total_price - $discountAmount
                ),
            ]);
        });
    }

    public function revoke(Order $order): void
    {
        if (! $order->coupon_id) {
            return;
        }

        DB::transaction(function () use ($order): void {
            $order->coupon?->decrement('used_count');

            CouponUsage::whereBelongsTo($order)->delete();

            $order->update([
                'coupon_id' => null,
                'discount_amount' => 0,
                'final_total' => (float) $order->total_price,
            ]);
        });
    }

    public function getStats(Coupon $coupon): array
    {
        return [
            'total_uses' => $coupon->used_count,
            'total_discount' => (float) $coupon->usages()->sum('discount_amount'),
            'unique_users' => $coupon->usages()
                ->distinct('user_id')
                ->count('user_id'),
            'remaining_uses' => $coupon->max_uses === null
                ? 'Unlimited'
                : max(0, $coupon->max_uses - $coupon->used_count),
        ];
    }

    private function findValidCoupon(string $code): Coupon
    {
        $coupon = Coupon::query()
            ->where('code', strtoupper(trim($code)))
            ->valid()
            ->first();

        if (! $coupon) {
            throw new CouponException(
                'Invalid or expired coupon code.'
            );
        }

        return $coupon;
    }

    private function assertMinOrderAmount(
        Coupon $coupon,
        float $orderAmount
    ): void {
        $minimumAmount = (float) $coupon->min_order_amount;

        if ($orderAmount < $minimumAmount) {
            throw new CouponException(sprintf(
                'A minimum order amount of %s is required to use this coupon.',
                $this->formatAmount($minimumAmount)
            ));
        }
    }

    private function assertGlobalUsageLimit(Coupon $coupon): void
    {
        if (
            $coupon->max_uses &&
            $coupon->used_count >= $coupon->max_uses
        ) {
            throw new CouponException(
                'This coupon has reached its maximum usage limit.'
            );
        }
    }

    private function assertPerUserUsageLimit(
        Coupon $coupon,
        User $user
    ): void {
        if ($user->hasReachedCouponLimit($coupon)) {
            throw new CouponException(
                'You have reached the usage limit for this coupon.'
            );
        }
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2) . ' ' . self::CURRENCY;
    }
}
