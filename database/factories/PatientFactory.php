<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->patient(),
            'insurance_number' => fake()->numerify('INS-########'),
            'emergency_contact' => fake()->phoneNumber(),
        ];
    }
}