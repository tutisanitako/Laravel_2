<?php

namespace Database\Seeders;

use App\Models\Visit;
use App\Models\Appointment;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        $completedAppointments = Appointment::where('status', 'completed')->get();

        foreach ($completedAppointments->take(10) as $appointment) {
            Visit::create([
                'appointment_id' => $appointment->id,
                'diagnosis' => fake()->sentence(10),
                'treatment' => fake()->paragraph(),
                'prescription' => fake()->paragraph(),
                'visit_date' => $appointment->appointment_date,
            ]);
        }

        // Create additional visits
        Visit::factory()->count(20)->create();
    }
}