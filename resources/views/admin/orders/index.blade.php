@extends('layouts.app')

@section('content')

    <h1>Orders</h1>

    <table>

        <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Total</th>
            <th>Status</th>
            <th></th>
        </tr>
        </thead>

        <tbody>

        @foreach($orders as $order)

            <tr>

                <td>{{ $order->id }}</td>

                <td>{{ $order->user->name }}</td>

                <td>{{ number_format($order->total_price, 2) }}</td>

                <td>{{ $order->status->value }}</td>

                <td>
                    <a href="{{ route('admins.orders.show', $order) }}">
                        View
                    </a>
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

    {{ $orders->links() }}

@endsection
