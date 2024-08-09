<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::query()->updateOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions(Permission::all()->pluck('name'));

        $roleUser = Role::query()->updateOrCreate(['name' => 'usuario']);
        $roleUser->syncPermissions(['cotizar', 'catalogo', 'user.historial', 'usuario']);
    }
}
