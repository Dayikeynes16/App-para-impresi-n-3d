<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
 
        Permission::query()->updateOrCreate(['name' => 'usuario']);
        Permission::query()->updateOrCreate(['name' => 'cotizar']);
        Permission::query()->updateOrCreate(['name' => 'catalogo']);
        Permission::query()->updateOrCreate(['name' => 'catalogo.editar']);
        Permission::query()->updateOrCreate(['name' => 'user.historial']);
        Permission::query()->updateOrCreate(['name' => 'admin.historial']);
        Permission::query()->updateOrCreate(['name' => 'dashboard']);
        Permission::query()->updateOrCreate(['name' => 'roles.permisos']);
        Permission::query()->updateOrCreate(['name' => 'usuarios']);
        Permission::query()->updateOrCreate(['name' => 'costo.produccion']);


        

        

    }
}
