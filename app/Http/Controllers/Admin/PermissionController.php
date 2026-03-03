<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionStoreRequest;
use App\Http\Requests\Admin\PermissionUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display permission catalog with role usage count.
     */
    public function index(): Response
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('name')
            ->paginate(15)
            ->through(fn (Permission $permission): array => [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles_count' => $permission->roles_count,
            ]);

        return Inertia::render('admin/permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a new permission string.
     */
    public function store(PermissionStoreRequest $request): RedirectResponse
    {
        Permission::query()->create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        return to_route('admin.permissions.index')->with('success', 'Permiso creado correctamente.');
    }

    /**
     * Update permission label.
     */
    public function update(PermissionUpdateRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update([
            'name' => $request->validated('name'),
        ]);

        return to_route('admin.permissions.index')->with('success', 'Permiso actualizado correctamente.');
    }

    /**
     * Delete permission.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return to_route('admin.permissions.index')->with('success', 'Permiso eliminado correctamente.');
    }
}
