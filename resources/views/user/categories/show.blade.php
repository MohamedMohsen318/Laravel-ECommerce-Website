@extends('layouts.app')

@section('title', $category->translate('en')?->name ?? $category->slug)

@section('content')
    <section class="stack">
        <div class="page-head">
            <div>
                <h1>{{ $category->translate('en')?->name ?? $category->slug }}</h1>
                <p class="muted">{{ $category->translate('en')?->description }}</p>
            </div>
            <a class="button secondary" href="{{ route('categories.index') }}">Back to Categories</a>
        </div>

        <div class="grid">
            @forelse ($children as $child)
                <article class="card stack">
                    <h2>{{ $child->translate('en')?->name ?? $child->slug }}</h2>
                    <a class="button secondary" href="{{ route('categories.show', $child->slug) }}">View Category</a>
                </article>
            @empty
                <p class="muted">No subcategories are available.</p>
            @endforelse
        </div>
    </section>
@endsection
