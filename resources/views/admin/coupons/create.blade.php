@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <h1 class="text-2xl font-bold mb-6">إضافة كوبون جديد</h1>

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('admins.coupons.store') }}">
                @csrf

                {{-- الكود --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">كود الكوبون *</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="w-full border rounded-lg px-3 py-2 uppercase @error('code') border-red-500 @enderror"
                           placeholder="SUMMER20">
                    @error('code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- النوع --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">نوع الخصم *</label>
                    <select name="type" id="discount-type"
                            class="w-full border rounded-lg px-3 py-2"
                            onchange="toggleDiscountFields()">
                        @foreach(\App\Enums\CouponType::cases() as $type)
                            <option value="{{ $type->value }}"
                                {{ old('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- القيمة --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">قيمة الخصم *</label>
                    <div class="flex">
                        <input type="number" name="value" value="{{ old('value') }}"
                               step="0.01" min="0.01"
                               class="flex-1 border rounded-r-lg px-3 py-2 @error('value') border-red-500 @enderror">
                        <span id="value-unit"
                              class="bg-gray-100 border border-r-0 rounded-l-lg px-3 py-2 text-gray-600">
                        %
                    </span>
                    </div>
                    @error('value')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- الحد الأقصى للخصم (للنسبة فقط) --}}
                <div class="mb-4" id="max-discount-field">
                    <label class="block text-sm font-medium mb-1">
                        الحد الأقصى لقيمة الخصم
                        <span class="text-gray-400 text-xs">(اختياري)</span>
                    </label>
                    <input type="number" name="max_discount_amount"
                           value="{{ old('max_discount_amount') }}"
                           step="0.01" min="0"
                           class="w-full border rounded-lg px-3 py-2"
                           placeholder="مثال: 200">
                </div>

                {{-- الحد الأدنى للشراء --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        الحد الأدنى للشراء
                        <span class="text-gray-400 text-xs">(اختياري)</span>
                    </label>
                    <input type="number" name="min_order_amount"
                           value="{{ old('min_order_amount', 0) }}"
                           step="0.01" min="0"
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                {{-- التواريخ --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">تاريخ البداية</label>
                        <input type="datetime-local" name="starts_at"
                               value="{{ old('starts_at') }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">تاريخ الانتهاء</label>
                        <input type="datetime-local" name="expires_at"
                               value="{{ old('expires_at') }}"
                               class="w-full border rounded-lg px-3 py-2 @error('expires_at') border-red-500 @enderror">
                        @error('expires_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- حدود الاستخدام --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">الحد الكلي للاستخدام</label>
                        <input type="number" name="max_uses" value="{{ old('max_uses') }}"
                               min="1" placeholder="بلا حد"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">الحد لكل مستخدم</label>
                        <input type="number" name="max_uses_per_user"
                               value="{{ old('max_uses_per_user', 1) }}"
                               min="1"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>

                {{-- التفعيل --}}
                <div class="mb-6 flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', '1') ? 'checked' : '' }}
                           class="w-4 h-4">
                    <label for="is_active" class="text-sm font-medium">الكوبون فعّال</label>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        إنشاء الكوبون
                    </button>
                    <a href="{{ route('admins.coupons.index') }}"
                       class="flex-1 text-center border py-2 rounded-lg hover:bg-gray-50">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleDiscountFields() {
                const type     = document.getElementById('discount-type').value;
                const unit     = document.getElementById('value-unit');
                const maxField = document.getElementById('max-discount-field');

                if (type === 'percentage') {
                    unit.textContent        = '%';
                    maxField.style.display  = 'block';
                } else {
                    unit.textContent        = 'ج.م';
                    maxField.style.display  = 'none';
                }
            }
            // تشغيل عند التحميل
            toggleDiscountFields();
        </script>
    @endpush
@endsection
