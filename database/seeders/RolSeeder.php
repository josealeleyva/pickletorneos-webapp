<?php

namespace Database\Seeders;

use App\Enums\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;


class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles del sistema
        if (!Role::where('name', Roles::Superadmin->value)->exists())
            Role::create(['name' => Roles::Superadmin->value]);

        if (!Role::where('name', Roles::Admin->value)->exists())
            Role::create(['name' => Roles::Admin->value]);

        if (!Role::where('name', Roles::Operador->value)->exists())
            Role::create(['name' => Roles::Operador->value]);

        if (!Role::where('name', Roles::Organizador->value)->exists())
            Role::create(['name' => Roles::Organizador->value]);

        if (!Role::where('name', Roles::Jugador->value)->exists())
            Role::create(['name' => Roles::Jugador->value]);


        // Asignando roles
        // Superadmin al primer usuario
        if (!DB::table('model_has_roles')->where('role_id', 1)->where('model_id', 1)->where('model_type', 'App\Models\User')->exists())
            DB::table('model_has_roles')->insert(['role_id' => 1, 'model_id' => 1, 'model_type' => 'App\Models\User']);
    }
}