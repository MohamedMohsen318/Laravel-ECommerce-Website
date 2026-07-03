@extends('layouts.app')

@section('title', $item->name)

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $t = fn (string $en, string $ar) => $isArabic ? $ar : $en;
    @endphp

    <section class="stack">
        <article class="detail-layout">
            <div class="detail-media">
                @if ($item->getFirstImageUrl())
                    <a class="media-link" href="{{ $item->getFirstImageUrl() }}" target="_blank" rel="noopener">
                        <img class="card-media" src="{{ $item->getFirstImageUrl() }}" alt="{{ $item->name }}">
                    </a>
                @else
                    <div class="image-placeholder">{{ $t('No Image', 'لا توجد صورة') }}</div>
                @endif
            </div>

            <div class="card stack">
                <div>
                    <h1>{{ $item->name }}</h1>
                    <p class="muted">{{ $item->description }}</p>
                </div>

                @if ($item->categories->isNotEmpty())
                    <div class="pill-list">
                        @foreach ($item->categories as $category)
                            <a class="pill" href="{{ route('categories.show', $category->fullPath()) }}">{{ $category->translate(app()->getLocale())?->name ?? $category->translate('en')?->name ?? $category->slug }}</a>
                        @endforeach
                    </div>
                @endif

                <div>
                    <div class="price">{{ $item->price }} EGP</div>
                    <p class="stock">{{ $t($item->stock . ' in stock', $item->stock . ' متوفر') }}</p>
                </div>

                <div class="actions">
                    <a class="button secondary" href="{{ route('products.index') }}">{{ $t('Back to Products', 'العودة للمنتجات') }}</a>
                    <form method="POST" action="{{ route('cart.add') }}" class="actions">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <input
                            type="number"
                            name="quantity"
                            value="1"
                            min="1"
                            max="{{ max(1, min(100, $item->stock)) }}"
                            style="width:82px"
                        >
                        <button class="button secondary" type="submit">{{ $t('Add to Cart', 'أضف للسلة') }}</button>
                    </form>
                    @auth
                        <form method="POST" action="{{ route('orders.store') }}">
                            @csrf
                            <input type="hidden" name="items[0][item_id]" value="{{ $item->id }}">
                            <input type="hidden" name="items[0][quantity]" value="1">
                            <button class="button" type="submit">{{ $t('Order Now', 'اطلب الآن') }}</button>
                        </form>
                    @else
                        <a class="button" href="{{ route('login') }}">{{ $t('Log in to Order', 'سجل الدخول للطلب') }}</a>
                    @endauth
                </div>
            </div>
        </article>

        @include('user.items.partials.reviews', ['item' => $item])
        @include('user.items.partials.comments', ['item' => $item])
    </section>
@endsection
