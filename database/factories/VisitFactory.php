<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'diagnosis' => fake()->sentence(10),
            'treatment' => fake()->paragraph(),
            'prescription' => fake()->optional()->paragraph(),
            'visit_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}