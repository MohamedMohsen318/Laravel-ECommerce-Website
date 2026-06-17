@php
    $count = 0;

    if (! \Illuminate\Support\Facades\Schema::hasTable('carts')) {
        $count = 0;
    } elseif (auth(\App\Enums\AuthGuard::Web->value)->check()) {
        $count = auth(\App\Enums\AuthGuard::Web->value)->user()->getCartItemsCount();
    } else {
        $cart = \App\Models\Cart::query()
            ->where('session_id', session()->getId())
            ->where('status', \App\Enums\CartStatus::ACTIVE->value)
            ->first();

        $count = (int) ($cart?->items()->sum('quantity') ?? 0);
    }
@endphp

<a href="{{ route('cart.index') }}"
   style="position:relative; display:inline-flex; align-items:center; gap:6px; font-weight:700; color:var(--ink)">
    Cart

    @if($count > 0)
        <span style="
            background:var(--danger);
            color:#fff;
            border-radius:999px;
            padding:2px 7px;
            font-size:12px;
            font-weight:900
        ">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</a>
