<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'revenue'    => 0,
            'orders'     => 0,
            'customers'  => User::count(),
            'items'      => Item::count(),
            'categories' => Category::count(),
        ];
    }

    public function getRecentOrders(): Collection
    {
        // هيتحدث لما تضيف orders table
        return collect([]);
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
