<div class="bg-gray-50 rounded-xl p-4 mb-4" id="discount-section">
    <h3 class="font-semibold text-gray-700 mb-3">كود الخصم</h3>

@if(session('applied_discount'))
    {{-- خصم مطبق --}}
    <div class="flex justify-between items-center bg-green-50 border border-green-200 rounded-lg p-3">
        <div>
            <span class="font-bold text-green-700">✅ {{ session('applied_discount.code') }}</span>
            <span class="text-sm text-gray-600 mr-2">
                    وفرت {{ number_format(session('applied_discount.discount_amount'), 2) }} ج.م
                </span>
        </div>
        <form method="POST" action="{{ route('discount.remove') }}">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-500 text-sm hover:underline">إلغاء ✕</button>
        </form>
    </div>
@else
    {{-- فورم الخصم --}}
    <div class="flex gap-2">
        <input type="text" id="discount-code"
               class="flex-1 border rounded-lg px-3 py-2 uppercase text-sm"
               placeholder="أدخل كود الخصم">
        <input type="hidden" id="order-amount" value="{{ $orderTotal }}">
        <button type="button" onclick="applyDiscount()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            تطبيق
        </button>
    </div>
    <div id="discount-msg" class="mt-2 text-sm hidden"></div>
    @endif
    </div>

    @push('scripts')
        <script>
            async function applyDiscount() {
                const code   = document.getElementById('discount-code').value.trim();
                const amount = document.getElementById('order-amount').value;
                const msgEl  = document.getElementById('discount-msg');

                if (!code) {
                    showDiscountMsg('error', 'أدخل كود الخصم أولاً');
                    return;
                }

                try {
                    const res = await fetch('{{ route("discount.apply") }}', {
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
                        showDiscountMsg('success', data.message);
                        // تحديث الإجمالي
                        const totalEl = document.getElementById('final-total');
                        if (totalEl) totalEl.textContent = parseFloat(data.final_amount).toFixed(2) + ' ج.م';
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showDiscountMsg('error', data.message ?? 'كود الخصم غير صحيح');
                    }
                } catch {
                    showDiscountMsg('error', 'حدث خطأ، حاول مرة أخرى');
                }
            }

            function showDiscountMsg(type, text) {
                const el = document.getElementById('discount-msg');
                el.className = 'mt-2 text-sm p-2 rounded ' +
                    (type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
                el.textContent = text;
                el.classList.remove('hidden');
            }
        </script>
    @endpush
