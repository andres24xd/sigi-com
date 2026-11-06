<?php

namespace Database\Factories;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetalleCompra>
 */
class DetalleCompraFactory extends Factory
{
    protected $model = DetalleCompra::class;

    public function definition(): array
    {
        return [
            'compra_id' => Compra::factory(),
            'producto_id' => Producto::factory(),
            'cantidad' => fake()->numberBetween(1, 20),
            'precio_unitario' => fake()->numberBetween(1000, 20000),
        ];
    }
}
