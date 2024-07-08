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
 

        Permission::create(['name' => 'cotizar']);
        Permission::create(['name' => 'catalogo']);
        Permission::create(['name' => 'catalogo.editar']);
        Permission::create(['name' => 'user.historial']);
        Permission::create(['name' => 'admin.historial']);
        Permission::create(['name' => 'dashboard']);

        

    }
}
