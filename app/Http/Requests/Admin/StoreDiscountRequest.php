<?php

namespace App\Http\Requests\Admin;

use App\Enums\AuthGuard;
use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth(AuthGuard::Admins->value)->check();
    }

    public function rules(): array
    {
        $discountId = $this->route('discount')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('discounts', 'code')->ignore($discountId),
            ],

            'type' => [
                'required',
                Rule::enum(DiscountType::class),
            ],

            'value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    if (
                        $this->type === DiscountType::Percentage->value &&
                        $value > 100
                    ) {
                        $fail('Discount percentage cannot be greater than 100%.');
                    }
                },
            ],

            'min_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_discount_amount' => [
                Rule::requiredIf(
                    $this->type === DiscountType::Percentage->value
                ),
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'boolean',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],

            'max_uses' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'max_uses_per_user' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Discount code is required.',
            'code.unique' => 'This discount code already exists.',
            'code.regex' => 'The discount code may only contain letters, numbers, dashes, and underscores.',
            'type.required' => 'Discount type is required.',
            'type.enum' => 'The selected discount type is invalid.',
            'value.required' => 'Discount value is required.',
            'expires_at.after' => 'The expiration date must be after the start date.',
            'max_discount_amount.required' => 'Maximum discount amount is required for percentage discounts.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->code
                ? strtoupper(trim($this->code))
                : null,

            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
