@extends('layouts.app')

@section('content')
    <section class="stack">
        <div class="page-head">
            <div>
                <h1>Discount Statistics</h1>
                <p class="muted">{{ $discount->code }} usage and discount history.</p>
            </div>

            <div class="actions">
                <a class="button secondary" href="{{ route('admins.discounts.edit', $discount) }}">Edit</a>
                <a class="button secondary" href="{{ route('admins.discounts.index') }}">Back to Discounts</a>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h3>Total Uses</h3>
                <p>{{ $stats['total_uses'] }}</p>
            </div>

            <div class="card">
                <h3>Total Discount</h3>
                <p>{{ number_format($stats['total_discount'], 2) }} EGP</p>
            </div>

            <div class="card">
                <h3>Unique Users</h3>
                <p>{{ $stats['unique_users'] }}</p>
            </div>

            <div class="card">
                <h3>Remaining Uses</h3>
                <p>{{ $stats['remaining_uses'] }}</p>
            </div>
        </div>

        <div class="card">
            <h2>Usage History</h2>

            <table class="table">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Order</th>
                    <th>Discount</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($usages as $usage)
                    <tr>
                        <td>{{ $usage->user?->name ?? 'Deleted user' }}</td>
                        <td>
                            @if ($usage->order)
                                #{{ $usage->order->id }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($usage->discount_amount, 2) }} EGP</td>
                        <td>{{ $usage->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center muted">No usage records yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $usages->links() }}
        </div>
    </section>
@endsection
