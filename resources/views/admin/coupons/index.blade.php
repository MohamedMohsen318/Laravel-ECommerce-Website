
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">إدارة الكوبونات</h1>
            <a href="{{ route('admins.coupons.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + إضافة كوبون
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
                   placeholder="بحث بالكود..."
                   class="border rounded-lg px-3 py-2 flex-1">
            <select name="status" class="border rounded-lg px-3 py-2">
                <option value="">كل الحالات</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>فعّال</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطّل</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg">بحث</button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600 border-b">
                <tr>
                    <th class="px-4 py-3">الكود</th>
                    <th class="px-4 py-3">النوع</th>
                    <th class="px-4 py-3">القيمة</th>
                    <th class="px-4 py-3">الاستخدامات</th>
                    <th class="px-4 py-3">إجمالي الخصم</th>
                    <th class="px-4 py-3">ينتهي في</th>
                    <th class="px-4 py-3">الحالة</th>
                    <th class="px-4 py-3">إجراءات</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono font-bold text-blue-700">
                            {{ $coupon->code }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $coupon->is_percentage ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $coupon->type->label() }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            @if($coupon->is_percentage)
                                {{ $coupon->value }}%
                            @else
                                {{ number_format($coupon->value, 2) }} ج.م
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ $coupon->usages_count }}
                            @if($coupon->max_uses)
                                <span class="text-gray-400">/ {{ $coupon->max_uses }}</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            {{ number_format($coupon->usages_sum_discount_amount ?? 0, 2) }} ج.م
                        </td>

                        <td class="px-4 py-3">
                            @if($coupon->expires_at)
                                {{ $coupon->expires_at->format('d/m/Y') }}
                                @if($coupon->expires_at->isPast())
                                    <span class="text-red-500 text-xs">(منتهي)</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">بلا حد</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <form method="POST"
                                  action="{{ route('admins.coupons.toggle', $coupon) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $coupon->is_active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700' }}">
                                    {{ $coupon->is_active ? 'فعّال' : 'معطّل' }}
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admins.coupons.edit', $coupon) }}"
                                   class="text-blue-600 hover:underline text-xs">تعديل</a>

                                <a href="{{ route('admins.coupons.stats', $coupon) }}"
                                   class="text-gray-600 hover:underline text-xs">إحصائيات</a>

                                <form method="POST"
                                      action="{{ route('admins.coupons.destroy', $coupon) }}"
                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:underline text-xs">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            لا توجد كوبونات بعد
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $coupons->withQueryString()->links() }}
        </div>
    </div>
@endsectio
