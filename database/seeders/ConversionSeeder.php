<?php

namespace Database\Seeders;

use App\Models\FactorConversion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConversionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        FactorConversion::query()->updateOrCreate([
            "conversion" => 1
        ]); 
    }
}
