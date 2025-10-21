<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->words(3, true),
            'description' => $this->faker->optional()->paragraph(2),
            'price'       => $this->faker->randomFloat(2, 5, 500),
            'image_url'   => $this->faker->optional()->imageUrl(640, 480, 'business', true),
            'active'      => $this->faker->boolean(90),
            'category_id' => Category::factory(),
        ];
    }
}