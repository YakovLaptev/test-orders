<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Customer::factory(3)->create();
        Product::factory(5)->create(['category' => 'Category 1']);
        Product::factory(5)->create(['category' => 'Category 2']);
    }
}
