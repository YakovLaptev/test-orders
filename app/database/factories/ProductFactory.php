<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'sku' => $this->faker->unique()->sentence(5),
            'price' => $this->faker->randomFloat(),
            'stock_quantity' => $this->faker->randomNumber(),
            'category' => $this->faker->word(),
        ];
    }
}
