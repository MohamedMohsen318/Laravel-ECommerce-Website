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

    public function rules(): array{
        $discountId = $this->route('discount')?->id;
        $isPercentage = $this->enum('type', DiscountType::class) === DiscountType::Percentage;
        $hasCondition = $this->boolean('is_condition');
         if ($this->isMethod('POST')) {
             return [
                 'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/',],
                 'type' => ['required', Rule::enum(DiscountType::class)],
                 'value' => ['required', 'numeric', 'min:0.01',
                     Rule::when($isPercentage, ['max:100']),],
                 'min_order_amount' => ['nullable', 'numeric', 'min:0'],
                 'max_discount_amount' => [Rule::requiredIf($isPercentage), 'nullable', 'numeric', 'min:0',],
                 'is_condition' => ['boolean'],
                 'min_condition_value' => ["required_if:is_condition,1", 'nullable', 'numeric', 'min:0',],
                 'max_condition_value' => ['nullable', 'numeric','min:0','gte:min_condition_value',],
                 'status' => ['required', Rule::in(['active', 'scheduled', 'cancelled'])],
                 'starts_at' => ['nullable', 'date'],
                 'expires_at' => ['nullable', 'date', 'after:starts_at'],
                 'max_uses' => ['nullable', 'integer', 'min:1'],
                 'max_uses_per_user' => ['nullable', 'integer', 'min:1', 'lte:max_uses',],
             ];
         }
         return ['code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/',
                 Rule::unique('discounts', 'code')->ignore($discountId),]
         ];
    }
    protected function prepareForValidation(): void
    {
        $hasCondition = $this->boolean('is_condition');

        $this->merge([
            'code' => $this->code ? strtoupper(trim((string) $this->code)) : null,
            'status' => $this->status ?: 'active',
            'is_condition' => $hasCondition,
            'min_condition_value' => $hasCondition ? $this->min_condition_value : null,
            'max_condition_value' => $hasCondition ? $this->max_condition_value : null,
        ]);
    }

}
