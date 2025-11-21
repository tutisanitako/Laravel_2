<?php

namespace Database\Factories;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $paymentMethods = ['cash', 'card', 'insurance'];
        $statuses = ['paid', 'unpaid'];

        return [
            'visit_id' => Visit::factory(),
            'amount' => fake()->randomFloat(2, 50, 500),
            'payment_method' => fake()->randomElement($paymentMethods),
            'status' => fake()->randomElement($statuses),
        ];
    }
}