<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $visits = Visit::all();

        foreach ($visits as $visit) {
            Payment::create([
                'visit_id' => $visit->id,
                'amount' => rand(50, 500),
                'payment_method' => ['cash', 'card', 'insurance'][rand(0, 2)],
                'status' => ['paid', 'unpaid'][rand(0, 1)],
            ]);
        }
    }
}