<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->firstName(),
            'last_name'         => $this->faker->lastName(),
            'second_last_name'  => $this->faker->lastName(),
            'phone_number'      => $this->faker->unique()->numerify('+591########'), // ajusta a tu formato
            'address'           => $this->faker->address(),
            'email'             => $this->faker->unique()->safeEmail(),
            'username'          => $this->faker->unique()->userName(),
            'password'          => bcrypt('password'), // o Hash::make('password')
            
            // Si tu migración mantiene estas columnas, puedes incluirlas:
            // 'email_verified_at' => now(),
            // 'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}