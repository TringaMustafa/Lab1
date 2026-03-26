<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 🔗 lidhje me user
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 💰 data kryesore
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->string('phone');

            // 💳 pagesa
            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('pending');

            $table->timestamps();

            // ⚡ index (bonus)
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};