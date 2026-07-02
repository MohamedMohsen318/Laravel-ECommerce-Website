<?php

namespace App\Services;

use App\Exceptions\DiscountException;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    private const CURRENCY = 'EGP';

    public function apply(Discount $discount, Order $order, User $user, float $discountAmount): void{
        DB::transaction(function () use ($discount, $order, $user, $discountAmount): void {
            $discount->recordUsage($user, $order, $discountAmount);
            $order->update([
                'discount_id' => $discount->id,
                'discount_amount' => $discountAmount,
                'final_total' => max(0, (float) $order->total_price - $discountAmount),
            ]);
        });
    }
    public function revoke(Order $order): void{
        if (! $order->discount_id) {
            return;
        }
        DB::transaction(function () use ($order): void {
            $order->discount?->decrement('used_count');

            DiscountUsage::whereBelongsTo($order)->delete();

            $order->update([
                'discount_id' => null,
                'discount_amount' => 0,
                'final_total' => (float) $order->total_price,
            ]);
        });
    }
    public function getStats(Discount $discount): array{
        return [
            'total_uses' => $discount->used_count,
            'total_discount' => (float) $discount->usages()->sum('discount_amount'),
            'unique_users' => $discount->usages()
                ->distinct('user_id')
                ->count('user_id'),
            'remaining_uses' => $discount->max_uses === null
                ? 'Unlimited'
                : max(0, $discount->max_uses - $discount->used_count),
        ];
    }
    private function findValidDiscount(string $code): Discount{
        $discount = Discount::query()
            ->where('code', strtoupper(trim($code)))
            ->valid()
            ->first();

        if (! $discount) {
            throw new DiscountException(
                'Invalid or expired discount code.'
            );
        }
        return $discount;
    }
    private function assertOrderAmountConstraints(Discount $discount, float $orderAmount): void{
        $minimumAmount = (float) $discount->min_order_amount;
        if ($orderAmount < $minimumAmount) {
            throw new DiscountException(sprintf(
                'A minimum order amount of %s is required to use this discount.',
                $this->formatAmount($minimumAmount)
            ));
        }
        if (! $discount->is_condition) {
            return;
        }
        $conditionMin = (float) $discount->min_condition_value;
        $conditionMax = $discount->max_condition_value !== null
            ? (float) $discount->max_condition_value
            : null;
        if ($orderAmount < $conditionMin) {
            throw new DiscountException(sprintf(
                'This discount requires an order amount of at least %s.',
                $this->formatAmount($conditionMin)
            ));
        }
        if ($conditionMax !== null && $orderAmount > $conditionMax) {
            throw new DiscountException(sprintf(
                'This discount can only be used for orders up to %s.',
                $this->formatAmount($conditionMax)
            ));
        }
    }
    private function assertGlobalUsageLimit(Discount $discount): void{
        if (
            $discount->max_uses &&
            $discount->used_count >= $discount->max_uses
        ) {
            throw new DiscountException(
                'This discount has reached its maximum usage limit.'
            );
        }
    }
    private function formatAmount(float $amount): string{
        return number_format($amount, 2) . ' ' . self::CURRENCY;
    }
    public function validate(string $code, float $orderAmount, ?User $user): array{
        $discount = $this->findValidDiscount($code);
        $this->assertOrderAmountConstraints($discount, $orderAmount);
        $this->assertGlobalUsageLimit($discount);

        $discountAmount = $discount->calculateDiscount($orderAmount);
        return [
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $orderAmount - $discountAmount),
            'message' => sprintf(
                'Discount applied successfully. You saved %s.',
                $this->formatAmount($discountAmount)
            ),
        ];
    }
}
