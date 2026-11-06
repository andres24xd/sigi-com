<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venta>
 */
class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'usuario_id' => User::factory(),
            'fecha' => fake()->dateTimeBetween('-1 month', 'now'),
            'total' => fake()->numberBetween(10000, 500000),
        ];
    }

    /**
     * Venta a consumidor final, sin cliente registrado.
     */
    public function consumidorFinal(): static
    {
        return $this->state(fn (array $attributes) => [
            'cliente_id' => null,
        ]);
    }
}
