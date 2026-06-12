<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $sales = $this->getSalesStats();

        return view('admin.dashboard', [
            'stats' => [
                'revenue' => $sales['revenue'],
                'orders' => $sales['orders'],
                'customers' => User::count(),
                'items' => Item::count(),
                'categories' => Category::count(),
                'admins' => Admin::count(),
            ],
            'recentOrders' => $sales['recentOrders'],
            'topCategories' => Category::with('translations')
                ->withCount('items')
                ->orderByDesc('items_count')
                ->limit(5)
                ->get(),
        ]);
    }

    private function getSalesStats(): array
    {
        if (! Schema::hasTable('orders')) {
            return [
                'revenue' => 0,
                'orders' => 0,
                'recentOrders' => collect(),
            ];
        }

        $totalColumn = Schema::hasColumn('orders', 'total')
            ? 'total'
            : (Schema::hasColumn('orders', 'total_price') ? 'total_price' : null);

        return [
            'revenue' => $totalColumn ? (float) DB::table('orders')->sum($totalColumn) : 0,
            'orders' => DB::table('orders')->count(),
            'recentOrders' => DB::table('orders')
                ->latest('created_at')
                ->limit(6)
                ->get(),
        ];
    }
}
