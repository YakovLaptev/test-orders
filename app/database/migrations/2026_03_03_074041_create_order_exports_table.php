<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_exports', function (Blueprint $table) {
            $table->id()->autoIncrement()->primary();
            $table->foreignIdFor(Order::class, 'order_id')->constrained('orders');
            $table->dateTime('exported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_exports');
    }
};
