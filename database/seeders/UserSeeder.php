<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::query()
            ->updateOrCreate([
                'email' => 'ventas@apscreativas.com',
            ], [
                'name' => 'Admin',
                'password' => 'Aplicaciones.2017',
                // 'is_visible' => 0,
            ]);

        $adminRole = Role::query()->updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminUser->syncRoles($adminRole);

        $publicUser = User::query()
            ->updateOrCreate([
                'email' => 'usuario@apscreativas.com',
            ], [
                'name' => 'Publico',
                'password' => 'Aplicaciones.2017',
                // 'is_visible' => 0,
            ]);

        $publicRole = Role::query()->updateOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
        $publicUser->syncRoles($publicRole);
    }
}
