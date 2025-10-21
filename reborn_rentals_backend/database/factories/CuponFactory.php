<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cupon>
 */
class CuponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['percent','fixed']);
        return [
            'code'            => strtoupper(Str::random(8)),
            'discount_type'   => $type,
            'discount_value'  => $type === 'percent'
                                   ? $this->faker->numberBetween(5, 50)      // 5–50 %
                                   : $this->faker->randomFloat(2, 5, 100),   // monto fijo
            'max_uses'        => $this->faker->optional()->numberBetween(1, 500),
            'min_order_total' => $this->faker->optional()->randomFloat(2, 20, 200),
            'starts_at'       => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'expires_at'      => $this->faker->optional()->dateTimeBetween('now', '+2 months'),
            'is_active'       => $this->faker->boolean(85),
        ];
    }
}