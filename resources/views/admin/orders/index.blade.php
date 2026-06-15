@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <section class="stack">

        <h1>Orders</h1>

        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user_id }}</td>
                        <td>{{ $order->status->value }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>
                            <a class="button secondary"
                               href="{{ route('admins.orders.show', $order->id) }}">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No orders yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </section>
@endsection
