<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'  => ['required', 'integer', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'options'  => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'  => 'The product is required.',
            'item_id.integer'   => 'Invalid product id.',
            'item_id.exists'    => 'The selected product does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer'  => 'Quantity must be a valid number.',
            'quantity.min'      => 'Minimum quantity is 1.',
            'quantity.max'      => 'Maximum quantity is 100.',
            'options.array'     => 'Options must be an array.',
        ];
    }
}
