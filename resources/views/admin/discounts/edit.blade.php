@extends('layouts.app')

@section('content')
    <section class="stack">
        <div class="page-head">
            <div>
                <h1>Edit Discount</h1>
                <p class="muted">Update {{ $discount->code }} settings and limits.</p>
            </div>

            <a class="button secondary" href="{{ route('admins.discounts.index') }}">Back to Discounts</a>
        </div>

        <div class="card">
            <form class="form" method="POST" action="{{ route('admins.discounts.update', $discount) }}">
                @csrf
                @method('PUT')

                <label class="field">
                    <span>Discount Code</span>
                    <input type="text" name="code" value="{{ old('code', $discount->code) }}" required>
                </label>

                <label class="field">
                    <span>Discount Type</span>
                    <select name="type" id="discount-type" onchange="toggleDiscountFields()" required>
                        <option value="percentage" @selected(old('type', $discount->type->value) === 'percentage')>Percentage</option>
                        <option value="fixed" @selected(old('type', $discount->type->value) === 'fixed')>Fixed Amount</option>
                    </select>
                </label>

                <label class="field">
                    <span>Discount Value</span>
                    <input type="number" name="value" value="{{ old('value', $discount->value) }}" step="0.01" min="0.01" required>
                </label>

                <label class="field" id="max-discount-field">
                    <span>Maximum Discount Amount</span>
                    <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $discount->max_discount_amount) }}" step="0.01" min="0">
                </label>

                <label class="field">
                    <span>Minimum Order Amount</span>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $discount->min_order_amount) }}" step="0.01" min="0">
                </label>

                <div class="grid">
                    <label class="field">
                        <span>Start Date</span>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($discount->starts_at)->format('Y-m-d\TH:i')) }}">
                    </label>

                    <label class="field">
                        <span>Expiration Date</span>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($discount->expires_at)->format('Y-m-d\TH:i')) }}">
                    </label>
                </div>

                <div class="grid">
                    <label class="field">
                        <span>Total Usage Limit</span>
                        <input type="number" name="max_uses" value="{{ old('max_uses', $discount->max_uses) }}" min="1">
                    </label>

                    <label class="field">
                        <span>Usage Limit Per User</span>
                        <input type="number" name="max_uses_per_user" value="{{ old('max_uses_per_user', $discount->max_uses_per_user) }}" min="1">
                    </label>
                </div>

                <label class="checkbox">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $discount->is_active))>
                    Discount is active
                </label>

                <div class="actions">
                    <button class="button" type="submit">Save Changes</button>
                    <a class="button secondary" href="{{ route('admins.discounts.stats', $discount) }}">View Statistics</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        function toggleDiscountFields() {
            const type = document.getElementById('discount-type').value;
            const maxField = document.getElementById('max-discount-field');

            maxField.style.display = type === 'percentage' ? 'grid' : 'none';
        }

        toggleDiscountFields();
    </script>
@endsection
