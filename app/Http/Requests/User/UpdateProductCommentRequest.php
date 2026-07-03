<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('comment')->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1000'],
        ];
    }
}
