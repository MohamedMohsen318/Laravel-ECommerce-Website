@extends('layouts.app')

@section('title', 'Cart #' . $cart->id)

@section('content')
    <section class="stack">

        <div class="page-head">
            <div>
                <h1>Cart #{{ $cart->id }}</h1>
                <p class="muted">Cart details and user information</p>
            </div>

            <a class="button secondary" href="{{ route('admins.carts.index') }}">
                ← Back
            </a>
        </div>

        <div style="display:grid; grid-template-columns:1fr 320px; gap:18px; align-items:start">

            {{-- Items --}}
            <div class="card" style="padding:0">

                <div style="padding:18px; border-bottom:1px solid var(--line); font-weight:700">
                    Items ({{ $cart->items->count() }})
                </div>

                <table class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($cart->items as $cartItem)
                        @php $item = $cartItem->item; @endphp

                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px">

                                    @if($item->getFirstImageUrl())
                                        <img src="{{ $item->getFirstImageUrl() }}"
                                             width="48" height="48"
                                             style="border-radius:6px; object-fit:cover">
                                    @endif

                                    <span style="font-weight:700">
                                        {{ $item->name }}
                                        @if ($cartItem->itemVariant)
                                            <small class="muted" style="display:block">{{ $cartItem->itemVariant->options_label }}</small>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td>{{ number_format($cartItem->price, 2) }} EGP</td>
                            <td>{{ $cartItem->quantity }}</td>
                            <td style="font-weight:900">
                                {{ number_format($cartItem->total, 2) }} EGP
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Sidebar --}}
            <div class="stack">

                {{-- Summary --}}
                <div class="card stack">

                    <h2 style="margin:0; font-size:18px">Summary</h2>

                    <div class="flex-between">
                        <span class="muted">Subtotal</span>
                        <span>{{ number_format($cart->subtotal, 2) }} EGP</span>
                    </div>

                    @if($cart->discount_amount > 0)
                        <div class="flex-between" style="color:#047857">
                            <span>Discount ({{ $cart->discount_code }})</span>
                            <span>-{{ number_format($cart->discount_amount, 2) }} EGP</span>
                        </div>
                    @endif

                    <div class="flex-between"
                         style="font-weight:900; font-size:17px; border-top:1px solid var(--line); padding-top:10px">

                        <span>Total</span>
                        <span>{{ number_format($cart->total, 2) }} EGP</span>
                    </div>

                </div>

                {{-- User --}}
                <div class="card stack">

                    <h2 style="margin:0; font-size:18px">User</h2>

                    @if($cart->user)
                        <div><strong>Name:</strong> {{ $cart->user->name }}</div>
                        <div><strong>Email:</strong> {{ $cart->user->email }}</div>
                    @else
                        <span class="muted">Guest (session)</span>
                    @endif

                </div>

                {{-- Status --}}
                <div class="card stack">

                    <h2 style="margin:0; font-size:18px">Status</h2>

                    <div>
                        Current:
                        <span class="pill">{{ $cart->status->label() }}</span>
                    </div>

                    <form method="POST"
                          action="{{ route('admins.carts.update-status', $cart) }}">

                        @csrf
                        @method('PATCH')

                        <div class="field">
                            <select name="status">
                                @foreach(\App\Enums\CartStatus::cases() as $status)
                                    <option value="{{ $status->value }}"
                                        @selected($cart->status === $status)>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button class="button" style="width:100%; margin-top:8px">
                            Update
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </section>
@endsection
