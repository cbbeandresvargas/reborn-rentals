<?php

namespace Database\Factories;
use App\Models\User; 
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentInfo>
 */
class PaymentInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $pan = $this->faker->creditCardNumber();

        return [
            'user_id'          => User::factory(), 
            'card_holder_name' => $this->faker->name(),
            'card_number'      => preg_replace('/\D/', '', $pan), // solo dígitos
            'card_expiration'  => $this->faker->creditCardExpirationDateString(), // MM/YY
            'cvv'              => $this->faker->numberBetween(100, 999),
        ];
    }
}