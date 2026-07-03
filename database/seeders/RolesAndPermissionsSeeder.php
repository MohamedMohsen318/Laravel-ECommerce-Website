<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Enums\AuthGuard;
use App\Enums\PermissionAction;
use App\Enums\PermissionGroup;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void{
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissionNames = $this->permissionNames();
        $this->deleteStaleDefaults($permissionNames);
        $this->deleteOrphanPermissionRows();
        foreach (AdminRole::values() as $roleName) {
            $this->createRole($roleName);
        }
        $superAdminRole = $this->createRole(AdminRole::SuperAdmin->value);
        $allPermissions = $this->createPermissions($permissionNames);
        $superAdminRole->givePermissionTo($allPermissions);
        $this->createSuperAdmin($superAdminRole);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
    private function createRole(string $name): Role{
        return Role::firstOrCreate([
            'name'       => $name,
            'guard_name' => AuthGuard::Admins->value,
        ]);
    }
    private function buildPermissionName(string $group, string $action): string{
        return "{$action}-{$group}";
    }
    private function permissionNames(): array{
        $permissionNames = [];
        foreach ($this->permissionGroups() as $group => $actions) {
            foreach ($actions as $action) {
                $permissionNames[] = $this->buildPermissionName($group, $action);
            }
        }
        return $permissionNames;
    }

    private function permissionGroups(): array
    {
        return [
            PermissionGroup::Products->value => PermissionAction::values(),
            PermissionGroup::Categories->value => PermissionAction::values(),
            PermissionGroup::Discounts->value => PermissionAction::values(),
            PermissionGroup::Orders->value => PermissionAction::values(),
            PermissionGroup::Admins->value => PermissionAction::values(),
            PermissionGroup::ProductFeedback->value => PermissionAction::values(),
        ];
    }
    private function createPermissions(array $permissionNames): array
    {
        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => AuthGuard::Admins->value,
            ]);
        }
        return $permissionNames;
    }
    private function deleteStaleDefaults(array $permissionNames): void{
        Role::where('guard_name', AuthGuard::LegacyAdmin->value)
            ->whereIn('name', [
                AdminRole::SuperAdmin->value,
                AuthGuard::LegacyAdmin->value,
                AdminRole::Editor->value,
            ])
            ->delete();
        Permission::where('guard_name', AuthGuard::LegacyAdmin->value)->delete();
        Role::where('guard_name', AuthGuard::Admins->value)
            ->whereNotIn('name', AdminRole::values())
            ->delete();
        Permission::where('guard_name', AuthGuard::Admins->value)
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
