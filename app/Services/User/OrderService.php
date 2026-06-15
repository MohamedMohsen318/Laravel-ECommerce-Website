<?php

namespace App\Services\User;

use App\Models\Order;
use App\Models\Item;
use App\Enums\ItemStatus;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(int $userId, array $items): Order
    {
        return DB::transaction(function () use ($userId, $items) {

            $total = 0;

            $order = Order::create([
                'user_id' => $userId,
                'status' => OrderStatus::PENDING,
                'total_price' => 0,
            ]);

            foreach ($items as $itemData) {

                $item = Item::findOrFail($itemData['item_id']);

                // 🧠 check availability
                if ($item->status !== ItemStatus::Available || $item->stock < $itemData['quantity']) {
                    throw new \Exception('Item not available or out of stock');
                }

                $price = $item->discount_price ?? $item->price;

                // create order item
                $order->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $price,
                ]);

                // decrease stock
                $item->decrement('stock', $itemData['quantity']);

                $total += $price * $itemData['quantity'];
            }

            $order->update([
                'total_price' => $total,
            ]);

            return $order;
        });
    }
}
