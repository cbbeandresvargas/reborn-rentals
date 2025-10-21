<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;      
use App\Models\PaymentInfo; 
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // crea 10 usuarios
        $users = User::factory()->count(10)->create();
        
        $users->each(function (User $user) {

            // Siempre puede tener PaymentInfos (o también opcional si quieres)
            if (fake()->boolean(80)) { // 80% de probabilidad
                PaymentInfo::factory()
                     ->for($user) // alternativa limpia
                    ->count(fake()->numberBetween(0, 2))
                    ->create();
            }
        });
    }
}