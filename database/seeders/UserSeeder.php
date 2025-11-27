<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->hasPosts(3)->create([
                'email' => 'admin@example.com',
                'name'  => 'Admin User',
            ]);

        User::factory()->count(5)->hasPosts(2)->create();


        \App\Models\Post::factory()->count(10)->create();
    }
}
