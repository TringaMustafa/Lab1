<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Customer info
            $table->string('customer_name');
            $table->string('phone');

            // Order
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('status', ['pending', 'paid', 'cancelled'])
                  ->default('pending');

            // Payment (REAL WORLD STANDARD)
            $table->string('payment_method')->default('cash'); // cash, card, paypal
            $table->string('payment_provider')->nullable();     // stripe, paypal
            $table->string('provider_ref')->nullable();         // session_id / order_id
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
