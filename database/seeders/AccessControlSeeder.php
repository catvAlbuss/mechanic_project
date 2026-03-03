<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    /**
     * Seed roles, permissions and training users.
     *
     * Educational objective:
     * - Show a reproducible setup for junior developers.
     * - Keep permission names explicit and business-friendly.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect([
            'users.manage',
            'roles.manage',
            'permissions.manage',
        ])->map(
            fn (string $name): Permission => Permission::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ])
        );

        $superAdminRole = Role::query()->firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $adminRole = Role::query()->firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $superAdminRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions->whereIn('name', ['users.manage', 'roles.manage']));

        $superAdmin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->syncRoles([$superAdminRole->id]);

        $inactiveUser = User::query()->firstOrCreate(
            ['email' => 'inactive@example.com'],
            [
                'name' => 'Usuario Desactivado',
                'password' => Hash::make('password'),
                'is_active' => false,
                'email_verified_at' => now(),
            ],
        );
        $inactiveUser->syncRoles([$adminRole->id]);
    }
}
