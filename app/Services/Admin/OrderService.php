<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function paginate(): LengthAwarePaginator{
        return Order::with([
            'user',
            'items.item.media',
            'items.itemVariant.optionValues.option',
        ])
            ->latest()
            ->paginate(15);
    }
    public function find(Order $order): Order{
        return $order->load([
            'user',
            'items.item.media',
            'items.itemVariant.optionValues.option',
        ]);
    }
}
