<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Services\User\AuthService;
use App\Services\User\CartService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected CartService $cartService
    ) {
        //
    }

    public function create(){
        return view('auth.register');
    }
    public function store(UserRegisterRequest $request){
        $guestSessionId = $request->session()->getId();

        auth()->login(
            $this->authService->register($request->validated())
        );
        $this->cartService->mergeGuestCart($guestSessionId);

        return redirect()->route('home');
    }
    public function edit(){
        return view('auth.login');
    }
    public function update(UserLoginRequest $request){
        $guestSessionId = $request->session()->getId();

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
        $this->cartService->mergeGuestCart($guestSessionId);

        return redirect()->route('home');
    }
    public function destroy(){
        $this->authService->logout();
        return redirect()->route('login');
    }
}
