<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ApplyDiscountRequest;
use App\Services\User\CartService;
use Illuminate\Http\RedirectResponse;

class CartDiscountController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function store(ApplyDiscountRequest $request): RedirectResponse
    {
        $result = $this->cartService->applyDiscount($request->code);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function destroy(): RedirectResponse
    {
        $this->cartService->removeDiscount();

        return back()->with('success', 'Discount removed successfully.');
    }
}
