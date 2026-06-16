@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

    <h1>
        Order #{{ $order->id }}
    </h1>

    <p>
        Customer:
        {{ $order->user->name }}
    </p>

    <p>
        Status:
        {{ $order->status->value }}
    </p>

    <p>
        Total:
        {{ number_format($order->total_price, 2) }}
        EGP
    </p>

    <table>

        <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>
        </thead>

        <tbody>

        @foreach ($order->items as $orderItem)

            <tr>

                <td>
                    {{ $orderItem->item?->name ?? 'Deleted Product' }}
                </td>

                <td>
                    {{ number_format($orderItem->price, 2) }}
                </td>

                <td>
                    {{ $orderItem->quantity }}
                </td>

                <td>
                    {{ number_format($orderItem->price * $orderItem->quantity, 2) }}
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

@endsection
