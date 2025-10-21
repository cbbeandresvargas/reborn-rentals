<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentMethod = $this->faker->numberBetween(1, 3); // 1=efectivo, 2=tarjeta, 3=transferencia (ejemplo)
        $discountTotal = $this->faker->optional(0.5)->randomFloat(2, 0, 30);
        $taxTotal      = $this->faker->optional(0.8)->randomFloat(2, 0, 25);
        $subtotal      = $this->faker->randomFloat(2, 20, 400);
        $total         = max(0, $subtotal - $discountTotal + $taxTotal);

        return [
            'total_amount'   => $total,
            'status'         => $this->faker->boolean(90),
            'discount_total' => $discountTotal,
            'ordered_at'     => $this->faker->dateTimeBetween('-15 days', 'now'),
            'payment_method' => $paymentMethod,
            'tax_total'      => $taxTotal,
            'transaction_id' => $this->faker->optional(0.7)->uuid(),
            'notes'          => $this->faker->optional()->sentence(12),
            'user_id'  => User::factory(),
            'job_id'   => Job::factory(),
            'cupon_id' => $this->faker->boolean(30) ? Cupon::factory() : null,
        ];
    }
}