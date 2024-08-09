<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrito;
use Illuminate\Support\Facades\DB;

class CarritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Carrito::factory()->count(200)->create();
        return;
        $carritos = [];

        for ($i = 0; $i < 50; $i++) {
            $carritos[] = [
                'usuario_id' => 2,
                'status' => 'pago confirmado',
                'direccion_id' => null, // Cambia esto según las IDs disponibles en tu tabla de direcciones
                'total' => rand(100, 1000), // Puedes ajustar esto según tus necesidades
                'recoleccion' => false, // O ajusta según tu lógica
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Carrito::insert($carritos);
    }
}