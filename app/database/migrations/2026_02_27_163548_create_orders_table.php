<?php

use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('customer_id');
            $table->foreignIdFor(Customer::class, 'customer')->constrained('customers');
            $table->string('status');
            $table->integer('total_amount');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->foreignIdFor(OrderItem::class, 'orderItems')->constrained('order_items');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
