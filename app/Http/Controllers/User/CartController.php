<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AddToCartRequest;
use App\Http\Requests\User\ApplyCouponRequest;
use App\Http\Requests\User\UpdateCartItemRequest;
use App\Services\User\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $cart = $this->cartService->getCart()
            ->load('items.item');

        return view('user.cart.index', compact('cart'));
    }

    public function add(AddToCartRequest $request): RedirectResponse
    {
        $this->cartService->addItem(
            $request->item_id,
            $request->quantity,
            $request->options ?? []
        );

        return back()->with('success', 'Item added to cart successfully.');
    }

    public function update(UpdateCartItemRequest $request, int $itemId): RedirectResponse
    {
        $this->cartService->updateItem(
            $itemId,
            $request->quantity
        );

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(int $itemId): RedirectResponse
    {
        $this->cartService->removeItem($itemId);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();

        return back()->with('success', 'Cart cleared successfully.');
    }

    public function applyCoupon(ApplyCouponRequest $request): RedirectResponse
    {
        $result = $this->cartService->applyCoupon($request->code);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cartService->removeCoupon();

        return back()->with('success', 'Coupon removed successfully.');
    }
}
