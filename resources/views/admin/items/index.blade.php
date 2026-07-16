@extends('layouts.app')

@section('title', 'Manage Products')

@section('content')
    <section class="stack">
        <div class="page-head">
            <h1>Manage Products</h1>
            <a class="button" href="{{ route('admins.items.create') }}">Add Product</a>
        </div>

        <div class="card">
            <table class="table">
                <thead>
                    <tr><th>Product</th><th>Categories</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>
                                {{ $item->name }}
                                @if ($item->has_variants)
                                    <small class="muted" style="display:block">{{ $item->children->count() }} variants</small>
                                @endif
                            </td>
                            <td>
                                {{ $item->categories->map(fn ($category) => $category->translate('en')?->name ?? $category->slug)->join(', ') ?: 'No category' }}
                            </td>
                            <td>
                                @if ($item->has_variants && $item->children->isNotEmpty())
                                    @php
                                        $activeVariantPrices = $item->children->where('is_active', true)->map->effective_price;
                                    @endphp
                                    {{ number_format($activeVariantPrices->min() ?? 0, 2) }} - {{ number_format($activeVariantPrices->max() ?? 0, 2) }} EGP
                                @else
                                    {{ number_format($item->effective_price, 2) }} EGP
                                @endif
                            </td>
                            <td>
                                {{ $item->effective_stock }}
                                @if ($item->has_variants)
                                    <small class="muted" style="display:block">from active variants</small>
                                @endif
                            </td>
                            <td>{{ $item->status->label() }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button secondary" href="{{ route('admins.items.edit', $item) }}">Edit</a>
                                    <form method="POST" action="{{ route('admins.items.destroy', $item) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">No products yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
