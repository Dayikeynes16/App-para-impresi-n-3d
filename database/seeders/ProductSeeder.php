<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::query()->updateOrCreate([
            'name' => 'Producto Personalizado',
            'description' => 'Los archivos son cargados por el usuario',
            'price' => 0,
            'is_custom' => true,
        ]);

    }
}
