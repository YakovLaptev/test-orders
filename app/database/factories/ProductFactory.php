<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'sku' => $this->faker->word(),
            'price' => $this->faker->randomFloat(),
            'stock_quantity' => $this->faker->randomNumber(),
            'category' => $this->faker->word(),
        ];
    }
}
