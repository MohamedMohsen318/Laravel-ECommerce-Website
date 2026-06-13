<?php


namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class AdminService
{
    public function paginate(): LengthAwarePaginator
    {
        return Admin::with('roles')
            ->where('email', '!=', 'superadmin@gmail.com')
            ->latest()
            ->paginate(10);
    }

    public function store(array $data): Admin
    {
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $admin->assignRole('editor');

        return $admin;
    }

    public function destroy(Admin $admin): void
    {
        $admin->delete();
    }
}
