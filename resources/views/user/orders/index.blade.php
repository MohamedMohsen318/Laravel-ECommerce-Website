@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <section class="stack">
        <h1>My Orders</h1>

        <div class="grid">
            @forelse ($orders as $order)
                <div class="card">
                    <h3>Order #{{ $order->id }}</h3>

                    <p>Status: {{ $order->status->value }}</p>
                    <p>Total: {{ $order->total_price }}</p>

                    <a href="{{ route('user.orders.show', $order->id) }}">
                        View Details
                    </a>
                </div>
            @empty
                <p class="muted">No orders yet.</p>
            @endforelse
        </div>
    </section>
@endsection
