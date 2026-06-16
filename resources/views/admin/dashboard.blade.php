@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .dashboard-head { display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap; }
        .metric { min-height: 128px; display: grid; align-content: space-between; gap: 14px; }
        .metric strong { display: block; font-size: 30px; color: #111827; }
        .metric span { color: #6b7280; }
        .dashboard-layout { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(280px, .6fr); gap: 16px; }
        .status-pill { display: inline-flex; border-radius: 999px; padding: 4px 10px; background: #ecfdf5; color: #047857; font-size: 13px; }
        @media (max-width: 860px) { .dashboard-layout { grid-template-columns: 1fr; } }
    </style>

    <section class="stack">
        <div class="dashboard-head">
            <div>
                <h1>Dashboard</h1>
                <p class="muted">Track store activity, products, categories, and orders from one workspace.</p>
            </div>
            <div class="actions">
                <a class="button secondary" href="{{ route('admins.categories.index') }}">Categories</a>
                <a class="button" href="{{ route('admins.items.index') }}">Products</a>
                @if (auth(\App\Enums\AuthGuard::Admins->value)->user()?->hasRole(\App\Enums\AdminRole::SuperAdmin->value))
                    <a class="button" href="{{ route('admins.admins.create') }}">Add Admin</a>
                    <a class="button secondary" href="{{ route('admins.admins.index') }}">Admins</a>
                    <a class="button" href="{{ route('admins.permissions.index') }}">Permissions</a>
                @endif
            </div>
        </div>

        <div class="grid">
            <article class="card metric"><span>Total Revenue</span><strong>${{ number_format($stats['revenue'], 2) }}</strong><small class="muted">From available orders</small></article>
            <article class="card metric"><span>Orders</span><strong>{{ number_format($stats['orders']) }}</strong><small class="muted">Store orders</small></article>
            <article class="card metric"><span>Customers</span><strong>{{ number_format($stats['customers']) }}</strong><small class="muted">Registered users</small></article>
            <article class="card metric"><span>Products</span><strong>{{ number_format($stats['items']) }}</strong><small class="muted">{{ number_format($stats['categories']) }} categories</small></article>
        </div>

        <div class="dashboard-layout">
            <section class="card">
                <h2>Recent Orders</h2>
                <table class="table">
                    <thead>
                    <tr><th>Order</th><th>Total</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>${{ number_format($order->total ?? $order->total_price ?? 0, 2) }}</td>
                            <td><span class="status-pill">{{ $order->status ?? 'new' }}</span></td>
                            <td>{{ $order->created_at ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="muted">No orders yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </section>

            <aside class="card">
                <h2>Top Categories</h2>
                <div class="stack">
                    @forelse ($topCategories as $category)
                        <div>
                            <strong>{{ $category->translate('en')?->name ?? $category->slug }}</strong>
                            <p class="muted">{{ $category->items_count }} products</p>
                        </div>
                    @empty
                        <p class="muted">No categories yet.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>
@endsection
