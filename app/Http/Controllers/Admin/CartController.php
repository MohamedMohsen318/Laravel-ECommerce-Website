<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCartStatusRequest;
use App\Models\Cart;
use App\Services\Admin\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'search']);

        $carts = $this->cartService->getAllCarts($filters);
        $stats = $this->cartService->getStats();

        return view('admin.cart.index', compact('carts', 'stats'));
    }

    public function show(Cart $cart): View
    {
        $cart->load(['user', 'items.item']);

        return view('admin.cart.show', compact('cart'));
    }

    public function updateStatus(
        UpdateCartStatusRequest $request,
        Cart $cart
    ): RedirectResponse {
        $this->cartService->updateStatus(
            $cart,
            $request->status
        );

        return back()->with('success', 'Cart status updated successfully.');
    }

    public function destroy(Cart $cart): RedirectResponse
    {
        $this->cartService->destroy($cart);

        return redirect()
            ->route('admins.carts.index')
            ->with('success', 'Cart deleted successfully.');
    }
}
