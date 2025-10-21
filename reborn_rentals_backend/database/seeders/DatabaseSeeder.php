<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class, // opcional
            CuponSeeder::class,
            JobSeeder::class,
            OrderSeeder::class,
            ContactSeeder::class, // si no lo haces dentro de UserSeeder
            PaymentInfoSeeder::class, // si no lo haces dentro de UserSeeder
        ]);
    }
}