@extends('layouts.app')

@section('title', $item->name)

@section('content')
    @php
        $isArabic = app()->getLocale() === 'ar';
        $t = fn (string $en, string $ar) => $isArabic ? $ar : $en;
        $variantOptionGroups = $item->children
            ->flatMap->attributeValues
            ->unique('id')
            ->groupBy('item_attribute_id');
    @endphp

    <section class="stack">
        <article class="detail-layout">
            <div class="detail-media">
                @if ($item->getFirstImageUrl())
                    <a class="media-link" href="{{ $item->getFirstImageUrl() }}" target="_blank" rel="noopener">
                        <img class="card-media" src="{{ $item->getFirstImageUrl() }}" alt="{{ $item->name }}">
                    </a>
                @else
                    <div class="image-placeholder">{{ $t('No Image', 'No Image') }}</div>
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
                            <a class="pill" href="{{ route('categories.show', $category->fullPath()) }}">
                                {{ $category->translate(app()->getLocale())?->name ?? $category->translate('en')?->name ?? $category->slug }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div>
                    <div class="price" id="product-price">{{ number_format($item->effective_price, 2) }} EGP</div>
                    <p class="stock" id="product-stock">{{ $t($item->effective_stock . ' in stock', $item->effective_stock . ' in stock') }}</p>
                </div>

                <div class="actions">
                    <a class="button secondary" href="{{ route('products.index') }}">{{ $t('Back to Products', 'Back to Products') }}</a>

                    <form method="POST" action="{{ route('cart.add') }}" class="actions">
                        @csrf
                        <input type="hidden" name="item_id" id="cart-item-id" value="{{ $item->id }}">

                        @if ($item->has_variants)
                            <div class="stack" id="variant-options" data-url="{{ route('products.variants', $item) }}" style="min-width:240px">
                                @foreach ($variantOptionGroups as $values)
                                    @php $attribute = $values->first()->attribute; @endphp
                                    <label class="field">
                                        <span>{{ $attribute?->name }}</span>
                                        <select class="variant-option-value">
                                            <option value="">Select {{ $attribute?->name }}</option>
                                            @foreach ($values->sortBy('value') as $value)
                                                <option value="{{ $value->id }}">{{ $value->value }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                @endforeach
                                <p class="muted" id="variant-message">Select all options</p>
                            </div>
                        @endif

                        <input
                            id="cart-quantity"
                            type="number"
                            name="quantity"
                            value="1"
                            min="1"
                            max="{{ max(1, min(100, $item->effective_stock)) }}"
                            style="width:82px"
                        >
                        <button class="button secondary" id="cart-submit" type="submit" @disabled($item->has_variants)>
                            {{ $t('Add to Cart', 'Add to Cart') }}
                        </button>
                    </form>

                    @auth
                        <form method="POST" action="{{ route('orders.store') }}">
                            @csrf
                            <input type="hidden" name="items[0][item_id]" id="order-item-id" value="{{ $item->id }}">
                            <input type="hidden" name="items[0][quantity]" id="order-quantity" value="1">
                            <button class="button" id="order-submit" type="submit" @disabled($item->has_variants)>
                                {{ $t('Order Now', 'Order Now') }}
                            </button>
                        </form>
                    @else
                        <a class="button" href="{{ route('login') }}">{{ $t('Log in to Order', 'Log in to Order') }}</a>
                    @endauth
                </div>
            </div>
        </article>

        @include('user.items.partials.reviews', ['item' => $item, 'reviews' => $reviews, 'myReview' => $myReview])
        @include('user.items.partials.comments', ['item' => $item, 'comments' => $comments])
    </section>

    @if ($item->has_variants)
        <script>
            const variantOptions = document.getElementById('variant-options');
            const optionSelects = [...document.querySelectorAll('.variant-option-value')];
            const cartItemInput = document.getElementById('cart-item-id');
            const orderItemInput = document.getElementById('order-item-id');
            const cartQuantityInput = document.getElementById('cart-quantity');
            const orderQuantityInput = document.getElementById('order-quantity');
            const cartSubmit = document.getElementById('cart-submit');
            const orderSubmit = document.getElementById('order-submit');
            const priceText = document.getElementById('product-price');
            const stockText = document.getElementById('product-stock');
            const variantMessage = document.getElementById('variant-message');

            const resetVariant = (message) => {
                cartItemInput.value = '{{ $item->id }}';

                if (orderItemInput) {
                    orderItemInput.value = '{{ $item->id }}';
                }

                cartSubmit.disabled = true;

                if (orderSubmit) {
                    orderSubmit.disabled = true;
                }

                variantMessage.textContent = message;
            };

            const syncQuantity = () => {
                if (orderQuantityInput) {
                    orderQuantityInput.value = cartQuantityInput.value || 1;
                }
            };

            const loadVariant = async () => {
                const selectedIds = optionSelects.map((select) => select.value).filter(Boolean);

                if (selectedIds.length !== optionSelects.length) {
                    resetVariant('Select all options');
                    return;
                }

                const url = new URL(variantOptions.dataset.url, window.location.origin);
                selectedIds.forEach((id) => url.searchParams.append('attribute_value_ids[]', id));

                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                const variant = data.variant;

                if (! variant || variant.stock < 1) {
                    resetVariant('This combination is not available');
                    return;
                }

                cartItemInput.value = variant.id;

                if (orderItemInput) {
                    orderItemInput.value = variant.id;
                }

                priceText.textContent = `${Number(variant.effective_price).toFixed(2)} EGP`;
                stockText.textContent = `${variant.stock} in stock`;
                cartQuantityInput.max = Math.min(100, variant.stock);
                syncQuantity();
                cartSubmit.disabled = false;

                if (orderSubmit) {
                    orderSubmit.disabled = false;
                }

                variantMessage.textContent = variant.options_label || variant.sku || 'Variant selected';
            };

            optionSelects.forEach((select) => select.addEventListener('change', loadVariant));
            cartQuantityInput?.addEventListener('input', syncQuantity);
        </script>
    @endif
@endsection
