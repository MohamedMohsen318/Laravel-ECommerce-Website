@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    <section class="stack" dir="rtl">

        <div class="page-head">
            <div>
                <h1>Cart</h1>
                <p class="muted">Review your items before checkout</p>
            </div>

            <a class="button secondary" href="{{ route('products.index') }}">
                ← Continue Shopping
            </a>
        </div>

        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        @if($cart->isEmpty())
            <div class="card text-center" style="padding:60px 24px">
                <p class="muted" style="font-size:18px">Your cart is empty</p>
                <a class="button" href="{{ route('products.index') }}">Shop Now</a>
            </div>
        @else
            <div style="display:grid; grid-template-columns:1fr 340px; gap:18px; align-items:start">

                {{-- Items --}}
                <div class="card" style="padding:0">

                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($cart->items as $cartItem)
                            @php $item = $cartItem->item; @endphp

                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:12px">

                                        @if($item->getFirstImageUrl())
                                            <img src="{{ $item->getFirstImageUrl() }}"
                                                 width="56" height="56"
                                                 style="border-radius:6px; object-fit:cover; border:1px solid var(--line)">
                                        @else
                                            <div class="image-placeholder" style="width:56px;height:56px">
                                                No image
                                            </div>
                                        @endif

                                        <span style="font-weight:700">{{ $item->name }}</span>
                                    </div>
                                </td>

                                <td>{{ number_format($cartItem->price, 2) }} EGP</td>

                                <td>
                                    <form method="POST" action="{{ route('cart.update', $cartItem->id) }}"
                                          style="display:flex; gap:6px; align-items:center">
                                        @csrf
                                        @method('PUT')

                                        <input type="number"
                                               name="quantity"
                                               value="{{ $cartItem->quantity }}"
                                               min="1"
                                               max="100"
                                               style="width:70px; text-align:center">

                                        <button class="button secondary">
                                            Update
                                        </button>
                                    </form>
                                </td>

                                <td style="font-weight:800">
                                    {{ number_format($cartItem->total, 2) }} EGP
                                </td>

                                <td>
                                    <form method="POST" action="{{ route('cart.remove', $cartItem->id) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button class="button danger">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div style="padding:16px; border-top:1px solid var(--line)">
                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            @method('DELETE')

                            <button class="button danger" onclick="return confirm('Clear cart?')">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="card stack">

                    <h2 style="margin:0; font-size:20px">Order Summary</h2>

                    {{-- Coupon --}}
                    <form method="POST" action="{{ route('cart.coupon.apply') }}">
                        @csrf

                        <label style="display:grid; gap:6px; font-weight:700; font-size:14px">
                            Coupon Code

                            <div style="display:flex; gap:8px">
                                <input type="text"
                                       name="code"
                                       placeholder="Enter coupon"
                                       value="{{ $cart->coupon_code ?? '' }}">

                                <button class="button">Apply</button>
                            </div>
                        </label>
                    </form>

                    @if($cart->coupon_code)
                        <form method="POST" action="{{ route('cart.coupon.remove') }}">
                            @csrf
                            @method('DELETE')

                            <div style="display:flex; gap:8px; align-items:center">
                                <span class="pill">{{ $cart->coupon_code }}</span>

                                <button class="button danger">
                                    Remove
                                </button>
                            </div>
                        </form>
                    @endif

                    <hr style="border:0; border-top:1px solid var(--line)">

                    <div class="flex-between">
                        <span class="muted">Subtotal</span>
                        <span>{{ number_format($cart->subtotal, 2) }} EGP</span>
                    </div>

                    @if($cart->discount_amount > 0)
                        <div class="flex-between" style="color:#047857">
                            <span>Discount</span>
                            <span>-{{ number_format($cart->discount_amount, 2) }} EGP</span>
                        </div>
                    @endif

                    <div class="flex-between" style="font-weight:900; font-size:18px">
                        <span>Total</span>
                        <span>{{ number_format($cart->total, 2) }} EGP</span>
                    </div>

                    @auth
                        <form method="POST" action="{{ route('orders.store') }}" class="stack">
                            @csrf
                            <input type="hidden" name="checkout_from_cart" value="1">
                            @foreach($cart->items as $cartItem)
                                <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $cartItem->item_id }}">
                                <input type="hidden" name="items[{{ $loop->index }}][quantity]" value="{{ $cartItem->quantity }}">
                            @endforeach
                            <button class="button" type="submit" style="width:100%">
                                Checkout
                            </button>
                        </form>
                    @else
                        <a class="button" href="{{ route('login') }}" style="width:100%; text-align:center">
                            Log in to Checkout
                        </a>
                    @endauth

                </div>

            </div>
        @endif

    </section>
@endsection
