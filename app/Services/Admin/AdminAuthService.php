<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdminAuthService
{
    public function login(array $credentials, bool $remember = false): bool
    {
        return Auth::guard('admins')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember);
    }

    public function register(array $data): Admin
    {
        return Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function logout(): void
    {
        Auth::guard('admins')->logout();
    }
}
