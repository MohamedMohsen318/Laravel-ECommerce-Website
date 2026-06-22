<?php
<div class="bg-gray-50 rounded-xl p-4 mb-4" id="coupon-section">
    <h3 class="font-semibold text-gray-700 mb-3">كوبون الخصم</h3>

@if(session('applied_coupon'))
    {{-- كوبون مطبق --}}
    <div class="flex justify-between items-center bg-green-50 border border-green-200 rounded-lg p-3">
        <div>
            <span class="font-bold text-green-700">✅ {{ session('applied_coupon.code') }}</span>
            <span class="text-sm text-gray-600 mr-2">
                    وفرت {{ number_format(session('applied_coupon.discount_amount'), 2) }} ج.م
                </span>
        </div>
        <form method="POST" action="{{ route('coupon.remove') }}">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-500 text-sm hover:underline">إلغاء ✕</button>
        </form>
    </div>
@else
    {{-- فورم الكوبون --}}
    <div class="flex gap-2">
        <input type="text" id="coupon-code"
               class="flex-1 border rounded-lg px-3 py-2 uppercase text-sm"
               placeholder="أدخل كود الكوبون">
        <input type="hidden" id="order-amount" value="{{ $orderTotal }}">
        <button type="button" onclick="applyCoupon()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            تطبيق
        </button>
    </div>
    <div id="coupon-msg" class="mt-2 text-sm hidden"></div>
    @endif
    </div>

    @push('scripts')
        <script>
            async function applyCoupon() {
                const code   = document.getElementById('coupon-code').value.trim();
                const amount = document.getElementById('order-amount').value;
                const msgEl  = document.getElementById('coupon-msg');

                if (!code) {
                    showCouponMsg('error', 'أدخل كود الكوبون أولاً');
                    return;
                }

                try {
                    const res = await fetch('{{ route("coupon.apply") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ code, order_amount: amount }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        showCouponMsg('success', data.message);
                        // تحديث الإجمالي
                        const totalEl = document.getElementById('final-total');
                        if (totalEl) totalEl.textContent = parseFloat(data.final_amount).toFixed(2) + ' ج.م';
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showCouponMsg('error', data.message ?? 'كود الكوبون غير صحيح');
                    }
                } catch {
                    showCouponMsg('error', 'حدث خطأ، حاول مرة أخرى');
                }
            }

            function showCouponMsg(type, text) {
                const el = document.getElementById('coupon-msg');
                el.className = 'mt-2 text-sm p-2 rounded ' +
                    (type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
                el.textContent = text;
                el.classList.remove('hidden');
            }
        </script>
    @endpush
