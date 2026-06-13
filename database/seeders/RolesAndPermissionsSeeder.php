<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    private const GUARD = 'admins';
    private const ROLES = ['super-admin', 'editor'];
    private const PERMISSION_GROUPS = [
        'products' => ['create', 'edit', 'delete', 'view'],
        'categories' => ['create', 'edit', 'delete', 'view'],
        'orders' => ['create', 'edit', 'delete', 'view'],
        'admins' => ['create', 'edit', 'delete', 'view'],
    ];
    public function run(): void{
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissionNames = $this->permissionNames();
        $this->deleteStaleDefaults($permissionNames);
        $this->deleteOrphanPermissionRows();
        foreach (self::ROLES as $roleName) {
            $this->createRole($roleName);
        }
        $superAdminRole = $this->createRole('super-admin');
        $allPermissions = $this->createPermissions($permissionNames);
        $superAdminRole->givePermissionTo($allPermissions);
        $this->createSuperAdmin($superAdminRole);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
    private function createRole(string $name): Role{
        return Role::firstOrCreate([
            'name'       => $name,
            'guard_name' => self::GUARD,
        ]);
    }
    private function buildPermissionName(string $group, string $action): string{
        return "{$action}-{$group}";
    }
    private function permissionNames(): array{
        $permissionNames = [];
        foreach (self::PERMISSION_GROUPS as $group => $actions) {
            foreach ($actions as $action) {
                $permissionNames[] = $this->buildPermissionName($group, $action);
            }
        }
        return $permissionNames;
    }
    private function createPermissions(array $permissionNames): array
    {
        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => self::GUARD,
            ]);
        }
        return $permissionNames;
    }
    private function deleteStaleDefaults(array $permissionNames): void{
        Role::where('guard_name', 'admin')
            ->whereIn('name', ['super-admin', 'admin', 'editor'])
            ->delete();
        Permission::where('guard_name', 'admin')->delete();
        Role::where('guard_name', self::GUARD)
            ->whereNotIn('name', self::ROLES)
            ->delete();
        Permission::where('guard_name', self::GUARD)
            ->whereNotIn('name', $permissionNames)
            ->delete();
    }
    private function deleteOrphanPermissionRows(): void{
        DB::table('model_has_roles')
            ->whereNotIn('role_id', Role::pluck('id'))
            ->delete();
        DB::table('role_has_permissions')
            ->whereNotIn('role_id', Role::pluck('id'))
            ->orWhereNotIn('permission_id', Permission::pluck('id'))
            ->delete();
        DB::table('model_has_permissions')
            ->whereNotIn('permission_id', Permission::pluck('id'))
            ->delete();
    }
    private function createSuperAdmin(Role $role): void{
        $admin = Admin::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            ['name'=> 'Super Admin', 'password' => Hash::make('12345678'),]
        );
        $admin->assignRole([$role]);
    }
}
