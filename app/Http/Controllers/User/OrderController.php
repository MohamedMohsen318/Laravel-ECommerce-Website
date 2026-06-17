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
            ->with('items.item.media')
            ->latest()
            ->paginate(10);

        return view(
            'user.orders.index',
            compact('orders')
        );
    }

    public function show(Order $order)
    {
        abort_unless(
            $order->user_id === auth()->id(),
            403
        );

        $order->load([
            'items.item.media',
        ]);

        return view(
            'user.orders.show',
            compact('order')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->orderService->createOrder(
            auth()->id(),
            $data['items']
        );

        if ($request->boolean('checkout_from_cart')) {
            $this->cartService->clearCart();
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Order created successfully.');
    }
}
