
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create-coupons');
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'code' => [
                'required', 'string', 'max:50', 'alpha_num',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'type'                => ['required', Rule::enum(\App\Enums\CouponType::class)],
            'value'               => ['required', 'numeric', 'min:0.01'],
            'min_order_amount'    => ['nullable', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active'           => ['boolean'],
            'starts_at'           => ['nullable', 'date'],
            'expires_at'          => ['nullable', 'date', 'after:starts_at'],
            'max_uses'            => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'      => 'كود الكوبون مطلوب',
            'code.unique'        => 'هذا الكود موجود بالفعل',
            'code.alpha_num'     => 'الكود يجب أن يحتوي على حروف وأرقام فقط',
            'type.required'      => 'نوع الخصم مطلوب',
            'value.required'     => 'قيمة الخصم مطلوبة',
            'expires_at.after'   => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->code) {
            $this->merge(['code' => strtoupper($this->code)]);
        }
    }
}
