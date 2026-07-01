@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <h1 class="text-2xl font-bold mb-6">Create New Discount</h1>

        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ route('admins.discounts.store') }}">
                @csrf

                {{-- Discount Code --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Discount Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="w-full border rounded-lg px-3 py-2 uppercase @error('code') border-red-500 @enderror"
                           placeholder="SUMMER20">
                    @error('code')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Discount Type --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Discount Type *</label>
                    <select name="type" id="discount-type"
                            class="w-full border rounded-lg px-3 py-2"
                            onchange="toggleDiscountFields()">
                        @foreach(\App\Enums\DiscountType::cases() as $type)
                            <option value="{{ $type->value }}"
                                {{ old('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Discount Value --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Discount Value *</label>
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

                {{-- Maximum Discount Amount (Percentage Only) --}}
                <div class="mb-4" id="max-discount-field">
                    <label class="block text-sm font-medium mb-1">
                        Maximum Discount Amount
                        <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="number" name="max_discount_amount"
                           value="{{ old('max_discount_amount') }}"
                           step="0.01" min="0"
                           class="w-full border rounded-lg px-3 py-2"
                           placeholder="Example: 200">
                </div>

                {{-- Minimum Order Amount --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">
                        Minimum Order Amount
                        <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <input type="number" name="min_order_amount"
                           value="{{ old('min_order_amount', 0) }}"
                           step="0.01" min="0"
                           class="w-full border rounded-lg px-3 py-2">
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start Date</label>
                        <input type="datetime-local" name="starts_at"
                               value="{{ old('starts_at') }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Expiration Date</label>
                        <input type="datetime-local" name="expires_at"
                               value="{{ old('expires_at') }}"
                               class="w-full border rounded-lg px-3 py-2 @error('expires_at') border-red-500 @enderror">
                        @error('expires_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Usage Limits --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Total Usage Limit</label>
                        <input type="number" name="max_uses" value="{{ old('max_uses') }}"
                               min="1" placeholder="Unlimited"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Usage Limit Per User</label>
                        <input type="number" name="max_uses_per_user"
                               value="{{ old('max_uses_per_user', 1) }}"
                               min="1"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>

                {{-- Active Status --}}
                <div class="mb-6 flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           {{ old('is_active', '1') ? 'checked' : '' }}
                           class="w-4 h-4">
                    <label for="is_active" class="text-sm font-medium">Discount is Active</label>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                        Create Discount
                    </button>
                    <a href="{{ route('admins.discounts.index') }}"
                       class="flex-1 text-center border py-2 rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleDiscountFields() {
                const type = document.getElementById('discount-type').value;
                const unit = document.getElementById('value-unit');
                const maxField = document.getElementById('max-discount-field');

                if (type === 'percentage') {
                    unit.textContent = '%';
                    maxField.style.display = 'block';
                } else {
                    unit.textContent = 'EGP';
                    maxField.style.display = 'none';
                }
            }

            // Run on page load
            toggleDiscountFields();
        </script>
    @endpush
@endsection
