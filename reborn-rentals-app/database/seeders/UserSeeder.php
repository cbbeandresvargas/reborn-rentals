<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador
        User::create([
            'name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@rebornrentals.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone_number' => '+1 555-000-0000',
            'address' => '401 Ryland St. Ste 200 A, Reno, NV 89502',
            'username' => 'admin',
        ]);

        // Usuario cliente regular
        User::create([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'cliente@example.com',
            'password' => Hash::make('cliente123'),
            'role' => 'user',
            'phone_number' => '+1 555-123-4567',
            'address' => '123 Main St, Reno, NV 89501',
            'username' => 'cliente',
        ]);
    }
}
