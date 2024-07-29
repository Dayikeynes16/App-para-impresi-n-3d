<?php

namespace Database\Seeders;

use App\Models\PrecioMinuto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostoProduccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrecioMinuto::query()->updateOrCreate([
            "precio" => 1.50
        ]);
    }
}
