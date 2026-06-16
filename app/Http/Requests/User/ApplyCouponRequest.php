<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'min:3', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Coupon code is required.',
            'code.string'   => 'Coupon code must be a valid string.',
            'code.min'      => 'Coupon code must be at least 3 characters.',
            'code.max'      => 'Coupon code cannot exceed 50 characters.',
        ];
    }
}
