<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ApplyDiscountRequest extends FormRequest
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
            'code.required' => 'Discount code is required.',
            'code.string'   => 'Discount code must be a valid string.',
            'code.min'      => 'Discount code must be at least 3 characters.',
            'code.max'      => 'Discount code cannot exceed 50 characters.',
        ];
    }
}
