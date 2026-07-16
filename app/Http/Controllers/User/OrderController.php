<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\User\CartService;
use App\Services\User\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CartService $cartService
    ) {}

    public function index()
    {
        $orders = auth()
            ->user()
            ->orders()
            ->with('items.item.media', 'items.item.attributeValues.attribute')
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items.item.media', 'items.item.attributeValues.attribute');

        return view('user.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $checkoutFromCart = $request->boolean('checkout_from_cart');

        $discountCode = $checkoutFromCart
            ? $this->cartService->getCart()->discount_code
            : null;

        $result = $this->orderService->createOrder(
            auth()->id(),
            $data['items'],
            $discountCode
        );

        if ($checkoutFromCart) {
            $this->cartService->clearCart();
        }

        $redirect = redirect()
            ->route('products.index')
            ->with('success', 'Order created successfully.');

        if ($result['discount_warning']) {
            $redirect->with('warning', $result['discount_warning']);
        }

        return $redirect;
    }
}
