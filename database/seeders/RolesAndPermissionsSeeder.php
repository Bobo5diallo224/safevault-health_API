<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'records.create',
            'records.view.own',
            'records.view.granted',
            'records.view.all',
            'records.update',
            'records.delete',

            'grants.create',
            'grants.revoke.own',
            'grants.revoke.all',
            'grants.view.own',
            'grants.view.all',

            'profiles.view.own',
            'profiles.update.own',
            'profiles.view.all',

            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $patient = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'sanctum']);
        $patient->syncPermissions([
            'records.view.own',
            'grants.create',
            'grants.revoke.own',
            'grants.view.own',
            'profiles.view.own',
            'profiles.update.own',
        ]);

        $doctor = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'sanctum']);
        $doctor->syncPermissions([
            'records.view.granted',
            'records.create',
            'records.update',
            'grants.view.own',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $admin->syncPermissions([
            'records.create',
            'records.view.all',
            'records.update',
            'records.delete',
            'grants.revoke.all',
            'grants.view.all',
            'profiles.view.all',
            'audit.view',
        ]);
    }
}