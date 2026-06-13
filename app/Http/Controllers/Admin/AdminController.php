<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    public function index()
    {
        return view('admin.admins.index', [
            'admins' => $this->adminService->paginate(),
        ]);
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $this->adminService->store(
            $request->only('name', 'email', 'password')
        );

        return redirect()->route('admins.admins.index')
            ->with('success', 'Admin created successfully');
    }

    public function destroy(Admin $admin)
    {
        $this->adminService->destroy($admin);

        return redirect()->route('admins.admins.index')
            ->with('success', 'Admin deleted successfully');
    }
}
