@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <section class="stack">

        <h1>Order #{{ $order->id }}</h1>

        <div class="card">
            <p><strong>Status:</strong> {{ $order->status->value }}</p>
            <p><strong>Total:</strong> {{ $order->total_price }}</p>
        </div>

        <div class="card">
            <h3>Items</h3>

            <table class="table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
                </thead>

                <tbody>
                @foreach ($order->items as $orderItem)
                    <tr>
                        <td>
                            {{ $orderItem->item->name ?? 'N/A' }}
                            <small class="muted" style="display:block">{{ $orderItem->item->options_label }}</small>
                        </td>
                        <td>{{ $orderItem->quantity }}</td>
                        <td>{{ $orderItem->price }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </section>
@endsection
