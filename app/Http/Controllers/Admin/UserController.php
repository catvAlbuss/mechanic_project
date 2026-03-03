<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display the user management page.
     *
     * The page receives:
     * - paginated users for table rendering
     * - role catalog for assignment
     * - permission catalog for direct permission assignment
     */
    public function index(): Response
    {
        $users = User::query()
            ->with(['roles:id,name', 'permissions:id,name'])
            ->latest()
            ->paginate(10)
            ->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'roles' => $user->roles->pluck('name')->values(),
                'role_ids' => $user->roles->pluck('id')->values(),
                'permissions' => $user->permissions->pluck('name')->values(),
                'permission_ids' => $user->permissions->pluck('id')->values(),
                'created_at' => $user->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'permissions' => Permission::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a new user and synchronize roles/permissions.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => $validated['is_active'],
        ]);

        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        return to_route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Update user profile and permission assignments.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->user()?->is($user) && ! $validated['is_active']) {
            return back()->withErrors([
                'is_active' => 'No puedes desactivar tu propia cuenta desde esta pantalla.',
            ]);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
            ...(! empty($validated['password']) ? ['password' => $validated['password']] : []),
        ]);

        $user->syncRoles($validated['roles'] ?? []);
        $user->syncPermissions($validated['permissions'] ?? []);

        return to_route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Toggle active/inactive state without editing other fields.
     *
     * This action is convenient for quick operational control from the table.
     */
    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return back()->withErrors([
                'is_active' => 'No puedes desactivar tu propia cuenta.',
            ]);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $state = $user->is_active ? 'activado' : 'desactivado';

        return to_route('admin.users.index')->with('success', "Usuario {$state} correctamente.");
    }

    /**
     * Delete user record.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return back()->withErrors([
                'user' => 'No puedes eliminar tu propia cuenta.',
            ]);
        }

        $user->delete();

        return to_route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
