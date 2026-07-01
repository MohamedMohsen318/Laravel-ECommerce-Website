<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyDiscountRequest extends FormRequest
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


}
