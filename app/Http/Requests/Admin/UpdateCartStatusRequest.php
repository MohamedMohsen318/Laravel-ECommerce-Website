<?php

namespace App\Http\Requests\Admin;

use App\Enums\CartStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                'in:' . implode(',', CartStatus::values()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.string'   => 'Status must be a valid string.',
            'status.in'       => 'Invalid cart status selected.',
        ];
    }
}
