<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admins')->check();
    }

    public function rules(): array
    {
        return [
            'parent_id'                    => 'nullable|exists:categories,id',
            'image'                        => 'nullable|image|max:2048',
            'is_active'                    => 'nullable|boolean',
            'translations.en.name'         => 'required|string|max:100',
            'translations.en.description'  => 'nullable|string',
            'translations.ar.name'         => 'nullable|string|max:100',
            'translations.ar.description'  => 'nullable|string',
        ];
    }
}
