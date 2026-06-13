<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    public function register(array $data): User
    {
        return User::create($data);
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function updateProfile(User $user, array $data): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        return $user->fresh();
    }
}
