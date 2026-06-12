<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        return view('admin.dashboard', [
            'stats' => $this->dashboardService->getStats(),
            'recentOrders' => $this->dashboardService->getRecentOrders(),
            'topCategories' => $this->dashboardService->getTopCategories(),
        ]);
    }
}
