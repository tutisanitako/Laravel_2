<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
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

        $doctorUsers = User::where('role', 'doctor')->get();

        foreach ($doctorUsers as $index => $user) {
            Doctor::create([
                'user_id' => $user->id,
                'specialization' => $specializations[$index % count($specializations)],
                'room_number' => 'Room-' . rand(100, 999),
            ]);
        }

        // Create additional doctors
        Doctor::factory()->count(10)->create();
    }
}