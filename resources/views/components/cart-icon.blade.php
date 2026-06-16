@php
    $count = 0;

    if (auth()->check()) {
        $count = auth()->user()->getCartItemsCount();
    } else {
        $count = \App\Models\Cart::query()
            ->where('session_id', session()->getId())
            ->where('status', \App\Enums\CartStatus::ACTIVE)
            ->value('items_count') ?? 0;
    }
@endphp

<a href="{{ route('cart.index') }}"
   style="position:relative; display:inline-flex; align-items:center; gap:6px; font-weight:700; color:var(--ink)">

    🛒 Cart

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
