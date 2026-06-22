<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyCouponRequest;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponService $couponService
    ) {}

    public function apply(ApplyCouponRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->couponService->validate(
            $request->validated('code'),
            (float) $request->validated('order_amount'),
            $request->user()
        );

        session()->put('applied_coupon', [
            'code' => $result['coupon']->code,
            'coupon_id' => $result['coupon']->id,
            'discount_amount' => $result['discount_amount'],
            'final_amount' => $result['final_amount'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'discount_amount' => $result['discount_amount'],
                'final_amount' => $result['final_amount'],
            ]);
        }

        return back()->with('coupon_applied', $result['message']);
    }

    public function remove(): JsonResponse|RedirectResponse
    {
        session()->forget('applied_coupon');

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Coupon removed successfully.');
    }
}
