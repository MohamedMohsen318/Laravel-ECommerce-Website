@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <section class="stack">
        <div class="page-head">
            <div>
                <h1>Products</h1>
                <p class="muted">Available items connected to the store categories for easy browsing.</p>
            </div>
            <a class="button secondary" href="{{ route('categories.index') }}">Browse Categories</a>
        </div>

        <div class="grid">
            @forelse ($items as $item)
                <article class="card stack">
                    <div>
                        <h2>{{ $item->name }}</h2>
                        <p class="muted">{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
                    </div>
                    <p><strong>Price:</strong> {{ $item->price }} EGP</p>
                    <p><strong>Stock:</strong> {{ $item->stock }}</p>
                    <a class="button" href="{{ route('products.show', $item) }}">View Product</a>
                </article>
            @empty
                <p class="muted">No products are available right now.</p>
            @endforelse
        </div>
    </section>
@endsection
