<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountRequest;
use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService $discountService
    ) {}

    public function index(): View
    {
        $discounts = Discount::withCount('usages')
            ->withSum('usages', 'discount_amount')
            ->when(request('search'), fn ($query, $search) => $query->where('code', 'like', "%{$search}%"))
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        return view('admin.discounts.create');
    }

    public function store(StoreDiscountRequest $request): RedirectResponse{
        Discount::create($request->validated());

        return redirect()
            ->route('admins.discounts.index')
            ->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount): View
    {
        $stats = $this->discountService->getStats($discount);

        return view('admin.discounts.edit', compact('discount', 'stats'));
    }

    public function show(Discount $discount): RedirectResponse
    {
        return redirect()->route('admins.discounts.stats', $discount);
    }

    public function update(StoreDiscountRequest $request, Discount $discount): RedirectResponse
    {
        $discount->update($request->validated());

        return back()->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $activeOrders = $discount->orders()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($activeOrders > 0) {
            return back()->withErrors([
                'discount' => 'Discount cannot be deleted while active orders are using it.',
            ]);
        }

        $discount->delete();

        return back()->with('success', 'Discount deleted successfully.');
    }

    public function toggle(Discount $discount): RedirectResponse
    {
        $discount->update([
            'status' => $discount->status === 'active' ? 'cancelled' : 'active',
        ]);

        $status = $discount->status === 'active' ? 'enabled' : 'disabled';

        return back()->with('success', "Discount {$status} successfully.");
    }

    public function stats(Discount $discount): View
    {
        $stats = $this->discountService->getStats($discount);
        $usages = $discount->usages()
            ->with(['user', 'order'])
            ->latest()
            ->paginate(20);

        return view('admin.discounts.stats', compact('discount', 'stats', 'usages'));
    }
}
