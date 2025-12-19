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
    Schema::table('users', function (Blueprint $table) {
        // Thêm cột xác thực email (cho chức năng đăng ký)
        $table->timestamp('email_verified_at')->nullable()->after('email');
        
        // Thêm cột trạng thái (cho admin quản lý)
        $table->string('status')->default('Active')->after('password');
        
        // Thêm cột remember token (cho chức năng ghi nhớ đăng nhập)
        $table->rememberToken()->after('status');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['email_verified_at', 'status', 'remember_token']);
    });
}
};
