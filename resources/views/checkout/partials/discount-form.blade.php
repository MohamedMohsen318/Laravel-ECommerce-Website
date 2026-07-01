<div class="bg-gray-50 rounded-xl p-4 mb-4" id="discount-section">
    <h3 class="font-semibold text-gray-700 mb-3">Discount Code</h3>

    @if(session('applied_discount'))
        {{-- Discount applied --}}
        <div class="flex justify-between items-center bg-green-50 border border-green-200 rounded-lg p-3">
            <div>
                <span class="font-bold text-green-700">✅ {{ session('applied_discount.code') }}</span>
                <span class="text-sm text-gray-600 mr-2">
                    You saved {{ number_format(session('applied_discount.discount_amount'), 2) }} EGP
                </span>
            </div>
            <form method="POST" action="{{ route('discount.remove') }}">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-500 text-sm hover:underline">Cancel ✕</button>
            </form>
        </div>
    @else
        {{-- Discount form --}}
        <div class="flex gap-2">
            <input type="text" id="discount-code"
                   class="flex-1 border rounded-lg px-3 py-2 uppercase text-sm"
                   placeholder="Enter discount code">
            <input type="hidden" id="order-amount" value="{{ $orderTotal }}">
            <button type="button" onclick="applyDiscount()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Apply
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
                showDiscountMsg('error', 'Please enter a discount code first');
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
                    // Update total
                    const totalEl = document.getElementById('final-total');
                    if (totalEl) totalEl.textContent = parseFloat(data.final_amount).toFixed(2) + ' EGP';
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showDiscountMsg('error', data.message ?? 'Invalid discount code');
                }
            } catch {
                showDiscountMsg('error', 'Something went wrong, please try again');
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
