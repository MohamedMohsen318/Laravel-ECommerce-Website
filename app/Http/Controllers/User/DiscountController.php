<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyDiscountRequest;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class DiscountController extends Controller
{
    public function __construct(
        private readonly DiscountService $discountService
    ) {}

    public function apply(ApplyDiscountRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->discountService->validate(
            $request->validated('code'),
            (float) $request->validated('order_amount'),
            $request->user()
        );

        session()->put('applied_discount', [
            'code' => $result['discount']->code,
            'discount_id' => $result['discount']->id,
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

        return back()->with('discount_applied', $result['message']);
    }

    public function remove(): JsonResponse|RedirectResponse
    {
        session()->forget('applied_discount');

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Discount removed successfully.');
    }
}
