<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patientUsers = User::where('role', 'patient')->get();

        foreach ($patientUsers as $user) {
            Patient::create([
                'user_id' => $user->id,
                'insurance_number' => 'INS-' . rand(10000000, 99999999),
                'emergency_contact' => fake()->phoneNumber(),
            ]);
        }

        // Create additional patients
        Patient::factory()->count(15)->create();
    }
}