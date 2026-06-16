@extends('layouts.app')

@section('title', 'Carts Management')

@section('content')
    <section class="stack" dir="rtl">

        <div class="page-head">
            <div>
                <h1>Carts</h1>
                <p class="muted">Manage all shopping carts</p>
            </div>
        </div>

        {{-- Stats --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px">
            @foreach([
                ['label' => 'Total Carts', 'value' => $stats['total']],
                ['label' => 'Active', 'value' => $stats['active']],
                ['label' => 'Abandoned', 'value' => $stats['abandoned']],
                ['label' => 'Completed', 'value' => $stats['completed']],
            ] as $stat)
                <div class="card" style="padding:18px">
                    <strong style="display:block; font-size:28px">
                        {{ $stat['value'] }}
                    </strong>
                    <span class="muted">{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="card">
            <form method="GET"
                  style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end">

                <div class="field" style="flex:1; min-width:200px">
                    <label>Search</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="User name or email">
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All</option>
                        @foreach(\App\Enums\CartStatus::cases() as $status)
                            <option value="{{ $status->value }}"
                                @selected(request('status') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="button">Filter</button>

                <a class="button secondary"
                   href="{{ route('admins.carts.index') }}">
                    Reset
                </a>
            </form>
        </div>

        {{-- Table --}}
        <div class="card" style="padding:0">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Coupon</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($carts as $cart)
                    <tr>
                        <td>{{ $cart->id }}</td>

                        <td>
                            @if($cart->user)
                                <div style="font-weight:700">
                                    {{ $cart->user->name }}
                                </div>
                                <div class="muted" style="font-size:13px">
                                    {{ $cart->user->email }}
                                </div>
                            @else
                                <span class="muted">Guest</span>
                            @endif
                        </td>

                        <td>{{ $cart->items_count }} items</td>

                        <td style="font-weight:900">
                            {{ number_format($cart->total, 2) }} EGP
                        </td>

                        <td>
                            @if($cart->coupon_code)
                                <span class="pill">{{ $cart->coupon_code }}</span>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>

                        <td>
                            <span class="pill">
                                {{ $cart->status->label() }}
                            </span>
                        </td>

                        <td style="font-size:13px">
                            {{ $cart->created_at->format('Y/m/d H:i') }}
                        </td>

                        <td>
                            <div class="actions">

                                <a class="button secondary"
                                   href="{{ route('admins.carts.show', $cart) }}"
                                   style="padding:6px 12px; min-height:unset; font-size:13px">
                                    View
                                </a>

                                <form method="POST"
                                      action="{{ route('admins.carts.destroy', $cart) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button class="button danger"
                                            style="padding:6px 12px; min-height:unset; font-size:13px"
                                            onclick="return confirm('Delete this cart?')">
                                        Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8"
                            style="text-align:center; padding:40px"
                            class="muted">
                            No carts found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $carts->links() }}

    </section>
@endsection
