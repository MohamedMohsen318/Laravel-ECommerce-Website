<?php

namespace App\Services\User;

use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use App\Models\Item;
use App\Models\Order;
use App\Services\DiscountException;
use App\Services\DiscountService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected DiscountService $discountService
    ) {}

    public function createOrder(int $userId, array $items, ?string $discountCode = null): array {
        return DB::transaction(function () use (
            $userId,
            $items,
            $discountCode
        ) {
            $total = 0;
            $order = Order::create([
                'user_id' => $userId,
                'status' => OrderStatus::PENDING,
                'total_price' => 0,
            ]);
            foreach ($items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);
                if (
                    $item->status !== ItemStatus::Available ||
                    $item->stock < $itemData['quantity']
                ) {
                    throw new \Exception(
                        'Item not available or out of stock'
                    );
                }
                $price = $item->discount_price ?? $item->price;
                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $price,
                ]);
                $item->decrement(
                    'stock',
                    $itemData['quantity']
                );
                $total +=
                    $price * $itemData['quantity'];
            }
            $order->update([
                'total_price' => $total,
            ]);
            $discountWarning = null;
            if ($discountCode) {
                $discountWarning = $this->applyDiscountToOrder(
                    $order,
                    $discountCode,
                    $total,
                    $userId
                );
            }
            return [
                'order' => $order->fresh(),
                'discount_warning' => $discountWarning,
            ];
        });
    }

    private function applyDiscountToOrder(Order $order, string $discountCode, float $orderTotal, int $userId): ?string {
        try {
            $result = $this->discountService->validate(
                $discountCode,
                $orderTotal,
                $order->user
            );
            $this->discountService->apply(
                $result['discount'],
                $order,
                $order->user,
                $result['discount_amount']
            );

            return null;
        } catch (DiscountException $exception) {
            return sprintf(
                'Discount code "%s" could not be applied: %s',
                $discountCode,
                $exception->getMessage()
            );
        }
    }
}
