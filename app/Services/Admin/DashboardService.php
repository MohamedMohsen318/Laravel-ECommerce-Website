<?php

namespace App\Services\Admin;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'revenue' => Order::where(
                'status',
                OrderStatus::COMPLETED
            )->sum('total_price'),

            'orders' => Order::count(),

            'customers' => User::count(),

            'items' => Item::count(),

            'categories' => Category::count(),
        ];
    }

    public function getRecentOrders(): Collection
    {
        return Order::with('user')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getTopCategories(): Collection
    {
        return Category::with('translations')
            ->withCount('items')
            ->whereNull('parent_id')
            ->orderByDesc('items_count')
            ->limit(5)
            ->get();
    }
}
