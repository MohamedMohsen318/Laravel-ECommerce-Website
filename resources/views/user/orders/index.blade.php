@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <section class="stack">

        <h1>My Orders</h1>

        <div class="grid">

            @forelse ($orders as $order)

                <div class="card">

                    <h3>
                        Order #{{ $order->id }}
                    </h3>

                    <p>
                        Status:
                        {{ $order->status->value }}
                    </p>

                    <p>
                        Total:
                        {{ number_format($order->total_price, 2) }}
                        EGP
                    </p>

                    <a href="{{ route('orders.show', $order) }}">
                        View Details
                    </a>

                </div>

            @empty

                <p class="muted">
                    No orders yet.
                </p>

            @endforelse

        </div>

        {{ $orders->links() }}

    </section>
@endsection
