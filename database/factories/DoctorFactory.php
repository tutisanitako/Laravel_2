<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        $specializations = [
            'Cardiology',
            'Dermatology',
            'Neurology',
            'Pediatrics',
            'Orthopedics',
            'General Medicine',
            'Psychiatry',
            'Ophthalmology',
        ];

        return [
            'user_id' => User::factory()->doctor(),
            'specialization' => fake()->randomElement($specializations),
            'room_number' => fake()->numerify('Room-###'),
        ];
    }
}