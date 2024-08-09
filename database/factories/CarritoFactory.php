<?php

namespace Database\Factories;

use App\Models\Carrito;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Carrito>
 */
class CarritoFactory extends Factory
{
    protected $model = Carrito::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => 2,
            'status' => fake()->randomElement([
                'Pago confirmado',
                'Activo',
                'Finalizado',
            ]),
            'direccion_id' => null,
            'total' => rand(1, 1000),
            'recoleccion' => true,
        ];
    }
}
