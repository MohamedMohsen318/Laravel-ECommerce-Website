<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    private array $defaultPermissions = [
        'view sales dashboard',
        'manage categories',
        'manage admins',
        'manage permissions',
    ];

    public function index(): View
    {
        $this->authorizeSuperAdmin();
        $this->ensureDefaultRolesAndPermissions();

        return view('admin.permissions.index', [
            'admins' => Admin::with('roles', 'permissions')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function edit(Admin $admin): View
    {
        $this->authorizeSuperAdmin();
        $this->ensureDefaultRolesAndPermissions();

        return view('admin.permissions.edit', [
            'admin' => $admin->load('roles', 'permissions'),
            'roles' => Role::where('guard_name', 'admin')
                ->orderBy('name')
                ->get(),
            'permissions' => Permission::where('guard_name', 'admin')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $this->ensureDefaultRolesAndPermissions();

        $data = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $admin->syncRoles($data['roles'] ?? []);
        $admin->syncPermissions($data['permissions'] ?? []);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Admin permissions updated successfully.');
    }

    private function authorizeSuperAdmin(): void
    {
        $admin = auth('admin')->user();
        $hasSuperAdmin = Admin::role('super-admin', 'admin')->exists();

        abort_unless(
            $admin && ($admin->hasRole('super-admin') || ! $hasSuperAdmin),
            403
        );
    }

    private function ensureDefaultRolesAndPermissions(): void
    {
        $permissions = collect($this->defaultPermissions)
            ->map(fn (string $permission) => Permission::findOrCreate($permission, 'admin'));

        $superAdmin = Role::findOrCreate('super-admin', 'admin');
        $admin = Role::findOrCreate('admin', 'admin');

        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions([
            'view sales dashboard',
            'manage categories',
        ]);
    }
}
