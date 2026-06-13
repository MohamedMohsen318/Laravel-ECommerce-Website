<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\User\AuthService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(UserRegisterRequest $request)
    {
        auth()->login(
            $this->authService->register($request->validated())
        );

        return redirect()->route('home');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(UserLoginRequest $request)
    {
        $success = $this->authService->login(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        if (! $success) {
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout()
    {
        $this->authService->logout();

        return redirect()->route('login');
    }

    public function showProfile()
    {
        return view('auth.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $this->authService->updateProfile(
            auth()->user(),
            $request->validated()
        );

        return back()->with('success', 'Profile updated successfully.');
    }
}
