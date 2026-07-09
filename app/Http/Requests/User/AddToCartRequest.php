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
            'item_variant_id' => ['nullable', 'integer', 'exists:item_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'options'  => ['nullable', 'array'],
        ];
    }

}
