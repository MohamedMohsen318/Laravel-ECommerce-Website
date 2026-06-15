@extends('layouts.app')

@section('title', $item->name)

@section('content')
    <section class="stack">
        <article class="card stack">
            <div>
                <h1>{{ $item->name }}</h1>
                <p class="muted">{{ $item->description }}</p>
            </div>

            <p><strong>Price:</strong> {{ $item->price }} EGP</p>
            <p><strong>Stock:</strong> {{ $item->stock }}</p>

            <div class="actions">
                <a class="button secondary" href="{{ route('products.index') }}">Back to Products</a>
                @auth
                    <form method="POST" action="{{ route('orders.store') }}">
                        @csrf
                        <input type="hidden" name="items[0][item_id]" value="{{ $item->id }}">
                        <input type="hidden" name="items[0][quantity]" value="1">
                        <button class="button" type="submit">Order Now</button>
                    </form>
                @else
                    <a class="button" href="{{ route('login') }}">Log in to Order</a>
                @endauth
            </div>
        </article>
    </section>
@endsection
