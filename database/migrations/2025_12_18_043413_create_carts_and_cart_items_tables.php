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
    // 1. Bảng Carts (Giỏ hàng chung)
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable(); // Nếu đã đăng nhập
        $table->string('session_id')->nullable(); // Nếu chưa đăng nhập (lưu session)
        $table->timestamps();
    });

    // 2. Bảng Cart Items (Chi tiết món hàng)
    Schema::create('cart_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
        $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
        $table->integer('quantity')->default(1);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts_and_cart_items_tables');
    }
};
