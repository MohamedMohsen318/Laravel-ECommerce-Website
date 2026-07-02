<div class="bg-gray-50 rounded-xl p-4 mb-4" id="discount-section">
    <h3 class="font-semibold text-gray-700 mb-3">Discount Code</h3>

    @if(isset($cart) && $cart->discount_code)
        <div class="flex justify-between items-center bg-green-50 border border-green-200 rounded-lg p-3">
            <div>
                <span class="font-bold text-green-700">Applied: {{ $cart->discount_code }}</span>
                <span class="text-sm text-gray-600 mr-2">
                    You saved {{ number_format($cart->discount_amount, 2) }} EGP
                </span>
            </div>

            <form method="POST" action="{{ route('cart.discount.remove') }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="text-red-500 text-sm hover:underline">
                    Cancel
                </button>
            </form>
        </div>
    @else
        <form class="flex gap-2" method="POST" action="{{ route('cart.discount.apply') }}">
            @csrf

            <input type="text"
                   name="code"
                   class="flex-1 border rounded-lg px-3 py-2 uppercase text-sm"
                   placeholder="Enter discount code">

            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                Apply
            </button>
        </form>
    @endif
</div>
