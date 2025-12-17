<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $pansCategory = Category::where('name', 'Washout Pans')->first();
        $lidsCategory = Category::where('name', 'Lids')->first();
        
        $products = [
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x8" - 18.25 ton, 271 gallons capacity',
                'price' => 20.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x28" - 20.25 ton, 587 gallons capacity',
                'price' => 25.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
            [
                'name' => 'Lid',
                'description' => '7\'x7\'x8" - 18.25 ton, 271 gallons capacity',
                'price' => 15.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $lidsCategory->id,
            ],
            [
                'name' => 'Lid',
                'description' => '7\'x7\'x28" - 20.25 ton, 587 gallons capacity',
                'price' => 18.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $lidsCategory->id,
            ],
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x8" - 18.25 ton, 271 gallons capacity',
                'price' => 22.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x28" - 20.25 ton, 587 gallons capacity',
                'price' => 28.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
            [
                'name' => 'Lid',
                'description' => '7\'x7\'x8" - 18.25 ton, 271 gallons capacity',
                'price' => 16.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $lidsCategory->id,
            ],
            [
                'name' => 'Lid',
                'description' => '7\'x7\'x28" - 20.25 ton, 587 gallons capacity',
                'price' => 19.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $lidsCategory->id,
            ],
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x8" - 18.25 ton, 271 gallons capacity',
                'price' => 21.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x28" - 20.25 ton, 587 gallons capacity',
                'price' => 26.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
