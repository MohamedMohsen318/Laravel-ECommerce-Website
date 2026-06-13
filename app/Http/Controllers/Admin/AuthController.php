<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Services\Admin\AdminAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AdminAuthService $authService
    ) {
        //
    }
    public function showLoginForm(){
        return view('admin.auth.login');
    }
    public function login(AdminLoginRequest $request){
        $success = $this->authService->login(
            $request->validated(),
            $request->boolean('remember')
        );
        if (! $success) {return back()->withErrors([
                'email' => 'Invalid credentials',
            ]);
        }
        $request->session()->regenerate();
        return redirect()->route('admins.dashboard');
    }
    public function logout(Request $request){
        $this->authService->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admins.login');
    }
}
