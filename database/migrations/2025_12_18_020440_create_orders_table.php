<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // account_id cũ
            $table->string('order_code')->unique();
            $table->string('receiver_name');
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone');
            $table->string('receiver_address');
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->default('COD');
            $table->string('order_status')->default('Pending'); // Pending, Processing, Completed, Cancelled
            $table->timestamps(); // Thay cho order_date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
