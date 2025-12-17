<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Washout Pans', 'description' => 'Concrete washout pans'],
            ['name' => 'Lids', 'description' => 'Lids for containers'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
