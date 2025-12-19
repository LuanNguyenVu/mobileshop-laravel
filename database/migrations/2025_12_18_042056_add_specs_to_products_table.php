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
    Schema::table('products', function (Blueprint $table) {
        // Thêm các cột thông số kỹ thuật
        $table->string('screen')->nullable();           // Màn hình
        $table->string('front_camera')->nullable();     // Cam trước
        // $table->string('camera');                    // Cam sau (đã có từ trước)
        $table->string('cpu')->nullable();              // CPU
        $table->string('gpu')->nullable();              // GPU
        $table->string('operating_system')->nullable(); // Hệ điều hành

        // Cột nội dung bài viết và thông số chi tiết (HTML)
        $table->longText('description')->nullable();    // Bài viết mô tả (thay cho 'article')
        $table->longText('detailed_specs')->nullable(); // Bảng cấu hình chi tiết HTML
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
