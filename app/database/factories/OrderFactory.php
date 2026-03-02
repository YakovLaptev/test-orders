<?php

namespace Database\Factories;

use App\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'customer_id' => $this->faker->randomNumber(),
            'status' => $this->faker->word(),
            'total_amount' => $this->faker->randomNumber(),
            'confirmed_at' => Carbon::now(),
            'shipped_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
