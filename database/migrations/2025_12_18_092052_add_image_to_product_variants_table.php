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
    Schema::table('product_variants', function (Blueprint $table) {
        // Thêm cột image sau cột color, cho phép null
        $table->string('image')->nullable()->after('color');
    });
}

public function down()
{
    Schema::table('product_variants', function (Blueprint $table) {
        $table->dropColumn('image');
    });
}

    /**
     * Reverse the migrations.
     */

};
