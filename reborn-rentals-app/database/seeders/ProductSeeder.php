<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $excavatorCategory = Category::where('name', 'Excavators')->first();
        $pansCategory = Category::where('name', 'Pans & Containers')->first();
        
        $products = [
            [
                'name' => 'Concrete Washout Pan',
                'description' => '7\'x7\'x27" - 18.25 ton, 587 gallons capacity',
                'price' => 20.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => $pansCategory->id,
            ],
            [
                'name' => 'Heavy Duty Excavator',
                'description' => '12\'x8\'x15" - 25 ton, 800 gallons capacity',
                'price' => 150.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $excavatorCategory->id,
            ],
            [
                'name' => 'Industrial Mixer',
                'description' => '5\'x5\'x20" - 12 ton, 400 gallons capacity',
                'price' => 85.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => Category::where('name', 'Mixers')->first()->id,
            ],
            [
                'name' => 'Construction Compactor',
                'description' => '8\'x6\'x30" - 22 ton, 700 gallons capacity',
                'price' => 120.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $excavatorCategory->id,
            ],
            [
                'name' => 'Utility Trailer',
                'description' => '6\'x4\'x18" - 15 ton, 500 gallons capacity',
                'price' => 45.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => Category::where('name', 'Trailers')->first()->id,
            ],
            [
                'name' => 'Material Handler',
                'description' => '10\'x8\'x25" - 20 ton, 650 gallons capacity',
                'price' => 95.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $excavatorCategory->id,
            ],
            [
                'name' => 'Concrete Pump',
                'description' => '12\'x10\'x35" - 30 ton, 900 gallons capacity',
                'price' => 200.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => Category::where('name', 'Mixers')->first()->id,
            ],
            [
                'name' => 'Crane Attachment',
                'description' => '4\'x4\'x15" - 10 ton, 300 gallons capacity',
                'price' => 75.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => $excavatorCategory->id,
            ],
            [
                'name' => 'Hydraulic System',
                'description' => '7\'x5\'x22" - 18 ton, 600 gallons capacity',
                'price' => 110.00,
                'image_url' => '/Product1.png',
                'active' => true,
                'category_id' => Category::where('name', 'Generators')->first()->id,
            ],
            [
                'name' => 'Power Generator',
                'description' => '5\'x3\'x16" - 8 ton, 250 gallons capacity',
                'price' => 65.00,
                'image_url' => '/Product2.png',
                'active' => true,
                'category_id' => Category::where('name', 'Generators')->first()->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
