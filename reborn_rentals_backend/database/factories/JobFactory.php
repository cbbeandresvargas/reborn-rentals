<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'latitude'  => $this->faker->randomFloat(7, -90, 90),
            'longitude' => $this->faker->randomFloat(7, -180, 180),
            'date'      => $this->faker->dateTimeBetween('-10 days', '+10 days')->format('Y-m-d'),
            'time'      => $this->faker->time('H:i:s'),
            'notes'     => $this->faker->optional()->sentence(10),
            'status'    => $this->faker->boolean(80),
        ];
    }
}