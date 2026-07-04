<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the global (guard-scoped) permissions used across organisations.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard');

        /** @var array<string> $permissions */
        $permissions = config('organisation.permissions', []);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, $guard);
        }
    }
}
