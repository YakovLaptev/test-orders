<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)->primary();
            $table->foreignIdFor(Order::class, 'order_id')->constrained('orders');
            $table->foreignIdFor(Product::class, 'product_id')->constrained('products');
            $table->integer('quantity');
            $table->float('unit_price');
            $table->float('total_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
