<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create.users']);
        Permission::create(['name' => 'create.rol']);
        Permission::create(['name' => 'create.permission']);

        // update cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'informatica']);
        $role->givePermissionTo('create.users');

        // or may be done by chaining
        $role = Role::create(['name' => 'admin'])
            ->givePermissionTo(['create.rol', 'create.users']);

        $superAdmin = Role::create(['name' => 'superadmin']);
        $superAdmin->givePermissionTo(Permission::all());


        // ===== USUARIO SUPERADMIN =====
        $user = User::firstOrCreate(
            ['email' => 'chuflun@chufluncico.com'],
            [
                'name' => 'Chuflun',
                'password' => Hash::make('Jarritas1981'),
            ]
        );
        $user->assignRole($superAdmin);


    }





}
