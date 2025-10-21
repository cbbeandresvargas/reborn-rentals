<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity   = $this->faker->numberBetween(1, 5);
        $unitPrice  = $this->faker->randomFloat(2, 5, 200);
        $lineTotal  = $quantity * $unitPrice;

        return [
            'order_id'   => Order::factory(),
            'product_id' => Product::factory(),
            'quantity'   => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ];
    }
}