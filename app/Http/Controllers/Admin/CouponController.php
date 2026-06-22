<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService
    ) {}

    public function index(): View
    {
        $coupons = Coupon::withCount('usages')
            ->withSum('usages', 'discount_amount')
            ->when(request('search'), fn ($query, $search) => $query->where('code', 'like', "%{$search}%"))
            ->when(request('status'), fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->latest()
            ->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::create($request->validated());

        return redirect()
            ->route('admins.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        $stats = $this->couponService->getStats($coupon);

        return view('admin.coupons.edit', compact('coupon', 'stats'));
    }

    public function update(StoreCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return back()->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $activeOrders = $coupon->orders()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($activeOrders > 0) {
            return back()->withErrors([
                'coupon' => 'Coupon cannot be deleted while active orders are using it.',
            ]);
        }

        $coupon->delete();

        return back()->with('success', 'Coupon deleted successfully.');
    }

    public function toggle(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);

        $status = $coupon->is_active ? 'enabled' : 'disabled';

        return back()->with('success', "Coupon {$status} successfully.");
    }

    public function stats(Coupon $coupon): View
    {
        $stats = $this->couponService->getStats($coupon);
        $usages = $coupon->usages()
            ->with(['user', 'order'])
            ->latest()
            ->paginate(20);

        return view('admin.coupons.stats', compact('coupon', 'stats', 'usages'));
    }
}
