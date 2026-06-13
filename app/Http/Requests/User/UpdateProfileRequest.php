<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array
    {
        $id = auth()->id();

        return [
            'name'     => 'required|string|max:100',
            'email'    => "required|email|unique:users,email,{$id}|max:100",
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
