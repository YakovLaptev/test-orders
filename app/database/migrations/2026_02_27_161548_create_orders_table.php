<?php

use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)->primary();
            $table->foreignIdFor(Customer::class, 'customer_id')->index()->constrained('customers');
            $table->string('status')->index();
            $table->float('total_amount');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->timestamps();
            $table->index(['confirmed_at', 'shipped_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
