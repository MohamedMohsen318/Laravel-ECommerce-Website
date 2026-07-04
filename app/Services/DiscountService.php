<?php

namespace App\Services;

use Exception;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    private const CURRENCY = 'EGP';

    public function validate(string $code, float $orderAmount, ?User $user): array {
        $discount = $this->findValidDiscount($code);
        $this->assertDiscountIsUsable($discount, $orderAmount, $user);
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

    public function apply(
        Discount $discount,
        Order $order,
        User $user,
        float $discountAmount
    ): void {
        DB::transaction(function () use (
            $discount,
            $order,
            $user,
            $discountAmount
        ) {
            $discount->recordUsage($user, $order, $discountAmount);

            $order->update([
                'discount_id' => $discount->id,
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
        if (! $order->discount_id) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->discount?->decrement('used_count');

            DiscountUsage::whereBelongsTo($order)->delete();

            $order->update([
                'discount_id' => null,
                'discount_amount' => 0,
                'final_total' => (float) $order->total_price,
            ]);
        });
    }

    public function getStats(Discount $discount): array
    {
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

    private function findValidDiscount(string $code): Discount
    {
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

    private function assertDiscountIsUsable(Discount $discount, float $orderAmount, ?User $user): void
    {
        $minConditionValue = (float) $discount->min_condition_value;
        $maxConditionValue = $discount->max_condition_value !== null
            ? (float) $discount->max_condition_value
            : null;

        $rules = [
            [$orderAmount < (float) $discount->min_order_amount, sprintf(
                'A minimum order amount of %s is required to use this discount.',
                $this->formatAmount((float) $discount->min_order_amount))],
            [$discount->is_condition && $orderAmount < $minConditionValue, sprintf(
                'This discount requires an order amount of at least %s.',
                $this->formatAmount($minConditionValue))],
            [$discount->is_condition && $maxConditionValue !== null && $orderAmount > $maxConditionValue, sprintf(
                'This discount can only be used for orders up to %s.',
                $this->formatAmount($maxConditionValue))],
            [$discount->max_uses && $discount->used_count >= $discount->max_uses,
                'This discount has reached its maximum usage limit.'],
            [$user && $discount->max_uses_per_user && $user->hasReachedDiscountLimit($discount),
                'You have reached the maximum number of uses for this discount.'],];
        foreach ($rules as [$condition, $message]) {
            if ($condition) {throw new DiscountException($message);}
        }
    }
    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2) . ' ' . self::CURRENCY;
    }
}

class DiscountException extends Exception
{
    public function render(): RedirectResponse
    {
        return back()->withErrors([
            'discount' => $this->getMessage(),
        ]);
    }
}
