<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RadiologiaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache de permisos (IMPORTANTE)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'modalidades' => ['view', 'create', 'edit', 'delete'],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                ]);
            }
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        // Asignar permisos
        $superadmin->givePermissionTo(Permission::all());
        $admin->givePermissionTo(Permission::all());       
    }
        
}