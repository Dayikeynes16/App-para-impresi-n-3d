<?php

namespace Database\Seeders;

use App\Models\CostoEnvio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostoEnvioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CostoEnvio::query()->updateOrCreate([
            "precio" => 250
        ]);
    }
}
