<?php

namespace Database\Factories;

use App\Models\Compra;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Compra>
 */
class CompraFactory extends Factory
{
    protected $model = Compra::class;

    public function definition(): array
    {
        return [
            'proveedor_id' => Proveedor::factory(),
            'fecha' => fake()->dateTimeBetween('-1 month', 'now'),
            'total' => fake()->numberBetween(50000, 1000000),
        ];
    }
}
