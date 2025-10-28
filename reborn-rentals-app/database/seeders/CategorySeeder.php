<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Excavators', 'description' => 'Heavy machinery for excavation'],
            ['name' => 'Mixers', 'description' => 'Concrete and material mixers'],
            ['name' => 'Pans & Containers', 'description' => 'Washout pans and containers'],
            ['name' => 'Generators', 'description' => 'Power generators for job sites'],
            ['name' => 'Trailers', 'description' => 'Utility trailers'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
