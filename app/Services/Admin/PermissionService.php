<?php
namespace App\Services\Admin;

use App\Models\Admin;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    private array $defaultPermissions = [
        'view sales dashboard',
        'manage categories',
        'manage admins',
        'manage permissions',
    ];

    public function ensureDefaults(): void
    {
        $permissions = collect($this->defaultPermissions)
            ->map(fn ($permission) =>
            Permission::findOrCreate($permission, 'admin')
            );

        $superAdmin = Role::findOrCreate('super-admin', 'admin');
        $admin = Role::findOrCreate('admin', 'admin');

        $superAdmin->syncPermissions($permissions);

        $admin->syncPermissions([
            'view sales dashboard',
            'manage categories',
        ]);
    }

    public function getAdmins()
    {
        return Admin::with('roles', 'permissions')
            ->orderBy('name')
            ->get();
    }

    public function getAdmin(Admin $admin)
    {
        return $admin->load('roles', 'permissions');
    }

    public function getRoles()
    {
        return Role::where('guard_name', 'admin')
            ->orderBy('name')
            ->get();
    }
    public function getPermissions()
    {
        return Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->get();
    }

    public function sync(Admin $admin, array $data): void
    {
        $admin->syncRoles($data['roles'] ?? []);
        $admin->syncPermissions($data['permissions'] ?? []);
    }

    public function authorizeSuperAdmin(): void
    {
        $admin = auth('admin')->user();

        $hasSuperAdmin = Admin::role('super-admin', 'admin')->exists();

        abort_unless(
            $admin && ($admin->hasRole('super-admin') || ! $hasSuperAdmin),
            403
        );
    }
}
