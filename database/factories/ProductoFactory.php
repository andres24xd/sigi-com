<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => ucfirst(fake()->words(3, true)),
            'categoria_id' => Categoria::factory(),
            'precio_venta' => fake()->numberBetween(1000, 50000),
            'stock_actual' => fake()->numberBetween(0, 100),
        ];
    }
}
