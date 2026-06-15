@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <section class="stack">
        <div class="page-head">
            <div>
                <h1>Categories</h1>
                <p class="muted">Start with the right category, then move smoothly into the products.</p>
            </div>
            <a class="button" href="{{ route('products.index') }}">All Products</a>
        </div>

        <div class="grid">
            @forelse ($categories as $category)
                <article class="card stack">
                    <div>
                        <h2>{{ $category->translate('en')?->name ?? $category->slug }}</h2>
                        <p class="muted">{{ $category->translate('en')?->description }}</p>
                    </div>
                    <a class="button secondary" href="{{ route('categories.show', $category->slug) }}">View Category</a>
                </article>
            @empty
                <p class="muted">No categories are available right now.</p>
            @endforelse
        </div>
    </section>
@endsection
