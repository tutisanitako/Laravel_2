<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = Doctor::all();

        foreach ($patients->take(10) as $patient) {
            Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctors->random()->id,
                'appointment_date' => now()->addDays(rand(1, 30)),
                'status' => 'pending',
                'notes' => 'Regular checkup',
            ]);
        }

        // Create additional appointments
        Appointment::factory()->count(30)->create();
    }
}