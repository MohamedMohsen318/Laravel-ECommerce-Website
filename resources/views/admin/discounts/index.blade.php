@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Discount Management</h1>
            <a href="{{ route('admins.discounts.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Add Discount
            </a>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="mb-4 flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by code..."
                   class="border rounded-lg px-3 py-2 flex-1">

            <select name="status" class="border rounded-lg px-3 py-2">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>

            <button type="submit"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg">
                Search
            </button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600 border-b">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Value</th>
                    <th class="px-4 py-3">Usage</th>
                    <th class="px-4 py-3">Total Discount</th>
                    <th class="px-4 py-3">Expires On</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
                </thead>

                <tbody class="divide-y">
                @forelse($discounts as $discount)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono font-bold text-blue-700">
                            {{ $discount->code }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $discount->is_percentage
                                    ? 'bg-purple-100 text-purple-700'
                                    : 'bg-orange-100 text-orange-700' }}">
                                {{ $discount->type->label() }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            @if($discount->is_percentage)
                                {{ $discount->value }}%
                            @else
                                {{ number_format($discount->value, 2) }} EGP
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ $discount->usages_count }}

                            @if($discount->max_uses)
                                <span class="text-gray-400">
                                    / {{ $discount->max_uses }}
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ number_format($discount->usages_sum_discount_amount ?? 0, 2) }} EGP
                        </td>

                        <td class="px-4 py-3">
                            @if($discount->expires_at)
                                {{ $discount->expires_at->format('d/m/Y') }}

                                @if($discount->expires_at->isPast())
                                    <span class="text-red-500 text-xs">(Expired)</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">Unlimited</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <form method="POST"
                                  action="{{ route('admins.discounts.toggle', $discount) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                        class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $discount->is_active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700' }}">
                                    {{ $discount->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admins.discounts.edit', $discount) }}"
                                   class="text-blue-600 hover:underline text-xs">
                                    Edit
                                </a>

                                <a href="{{ route('admins.discounts.stats', $discount) }}"
                                   class="text-gray-600 hover:underline text-xs">
                                    Statistics
                                </a>

                                <form method="POST"
                                      action="{{ route('admins.discounts.destroy', $discount) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this discount?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:underline text-xs">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            No discounts found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $discounts->withQueryString()->links() }}
        </div>
    </div>
@endsection
