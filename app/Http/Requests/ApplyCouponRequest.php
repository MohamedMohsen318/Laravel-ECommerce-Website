<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'code'         => ['required', 'string', 'max:50'],
            'order_amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'         => 'The coupon code is required.',
            'code.string'           => 'The coupon code must be a string.',
            'code.max'              => 'The coupon code may not be greater than 50 characters.',
            'order_amount.required' => 'The order amount is required.',
            'order_amount.numeric'  => 'The order amount must be a number.',
            'order_amount.min'      => 'The order amount must be at least 0.',
        ];
    }
}
