<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required.',
            'quantity.integer'  => 'Quantity must be a valid number.',
            'quantity.min'      => 'Quantity cannot be less than 0.',
            'quantity.max'      => 'Quantity cannot exceed 100.',
        ];
    }
}
