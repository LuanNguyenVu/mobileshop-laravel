<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Kiểm tra xem admin đã tồn tại chưa để tránh lỗi trùng lặp
        if (!Admin::where('email', 'admin@gmail.com')->exists()) {
            Admin::create([
                'username' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'), // Mật khẩu giải mã là 12345678
                'role' => 'admin',
            ]);
            echo "Đã tạo tài khoản Admin: admin@gmail.com / 12345678 \n";
        } else {
            echo "Tài khoản Admin đã tồn tại! \n";
        }
    }
}