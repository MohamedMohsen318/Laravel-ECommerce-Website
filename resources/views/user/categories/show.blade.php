@extends('layouts.app')

@section('title', $category->translate('en')?->name ?? $category->slug)

@section('content')
    <section class="stack">
        <div class="card">
            <h1>{{ $category->translate('en')?->name ?? $category->slug }}</h1>
            <p class="muted">{{ $category->translate('en')?->description }}</p>
        </div>

        <div class="grid">
            @forelse ($category->children as $child)
                <article class="card">
                    <h2>{{ $child->translate('en')?->name ?? $child->slug }}</h2>
                    {{--
                        FIX #11: بنبني الـ full path بشكل صح
                        لو category عندها parent، الـ path لازم يشمله
                        مثال: electronics/phones/samsung
                    --}}
                    <a href="{{ route('categories.show', $category->fullPath() . '/' . $child->slug) }}">
                        View category
                    </a>
                </article>
            @empty
                <p class="muted">No subcategories.</p>
            @endforelse
        </div>
    </section>
@endsection
