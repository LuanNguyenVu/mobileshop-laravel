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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->string('ram')->nullable();
            $table->string('rom')->nullable();
            $table->string('camera')->nullable();
            $table->string('battery')->nullable();
            $table->enum('status', ['in_stock', 'out_of_stock', 'hidden'])->default('in_stock');
            $table->timestamps(); // Thay cho created_date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
