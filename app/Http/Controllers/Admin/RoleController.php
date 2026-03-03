<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display roles with their linked permissions.
     */
    public function index(): Response
    {
        $roles = Role::query()
            ->with('permissions:id,name')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10)
            ->through(fn (Role $role): array => [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'permissions' => $role->permissions->pluck('name')->values(),
                'permission_ids' => $role->permissions->pluck('id')->values(),
            ]);

        return Inertia::render('admin/roles/Index', [
            'roles' => $roles,
            'permissions' => Permission::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store role and assign selected permissions.
     */
    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::query()->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return to_route('admin.roles.index')->with('success', 'Rol creado correctamente.');
    }

    /**
     * Update role metadata and its permissions.
     */
    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return to_route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Delete role unless it is the base administrative role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return back()->withErrors([
                'role' => 'El rol Super Admin está protegido y no puede eliminarse.',
            ]);
        }

        $role->delete();

        return to_route('admin.roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
