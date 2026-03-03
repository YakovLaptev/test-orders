<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_exports', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->foreignIdFor(Order::class, 'order_id')->constrained('orders');
            $table->dateTime('exported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_exports');
    }
};
