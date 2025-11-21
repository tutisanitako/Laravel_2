<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@hospital.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'address' => '123 Admin Street',
        ]);

        // Create Receptionist
        User::create([
            'name' => 'Receptionist User',
            'email' => 'receptionist@hospital.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'phone' => '+1234567891',
            'address' => '124 Reception Street',
        ]);

        // Create additional users
        User::factory()->count(5)->patient()->create();
        User::factory()->count(5)->doctor()->create();
    }
}