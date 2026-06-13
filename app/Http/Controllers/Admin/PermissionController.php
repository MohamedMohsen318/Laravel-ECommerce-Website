<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\Admin\PermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    public function index(): View
    {
        $this->permissionService->authorizeSuperAdmin();
        $this->permissionService->ensureDefaults();

        return view('admin.permissions.index', [
            'admins' => $this->permissionService->getAdmins(),
        ]);
    }

    public function edit(Admin $admin): View
    {
        $this->permissionService->authorizeSuperAdmin();
        $this->permissionService->ensureDefaults();

        return view('admin.permissions.edit', [
            'admin' => $this->permissionService->getAdmin($admin),
            'roles' => $this->permissionService->getRoles(),
            'permissions' => $this->permissionService->getPermissions(),
        ]);
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $this->permissionService->authorizeSuperAdmin();
        $this->permissionService->ensureDefaults();

        $data = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $this->permissionService->sync($admin, $data);

        return redirect()
            ->route('admins.permissions.index')
            ->with('success', 'Admin permissions updated successfully.');
    }
}
