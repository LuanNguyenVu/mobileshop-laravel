<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Để dùng Auth::login, Auth::attempt
use Illuminate\Support\Facades\Hash; // <--- BẠN ĐANG THIẾU DÒNG NÀY (Để dùng Hash::make)
use Illuminate\Auth\Events\Registered; // Để dùng event(new Registered)
use App\Models\User;                // <--- ĐÂY LÀ DÒNG BẠN ĐANG THIẾU
use App\Models\Admin;               // Import luôn Admin để dùng cho đăng nhập

class AuthController extends Controller
{
    // 1. Hiển thị form Login (Giữ nguyên)
    public function showLogin() {
        // Nếu đã là User -> Home
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        // Nếu đã là Admin -> Dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    // 3. Hiển thị form Đăng ký
    public function showRegister() {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }
    // XỬ LÝ ĐĂNG NHẬP (ĐÃ GỘP CẢ 2 TÍNH NĂNG)
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        // --- LẦN QUÉT 1: KIỂM TRA TÀI KHOẢN NGƯỜI DÙNG (USER) ---
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            // >> KIỂM TRA KÍCH HOẠT EMAIL <<
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice'); 
            }

            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        // --- LẦN QUÉT 2: KIỂM TRA TÀI KHOẢN QUẢN TRỊ (ADMIN) ---
        // Nếu không phải User, hệ thống sẽ chạy xuống đây kiểm tra Admin
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Admin thường không cần kích hoạt email, cho vào luôn
            return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Admin quay trở lại!');
        }

        // --- CẢ 2 ĐỀU SAI ---
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản không tồn tại.',
        ]);
    }

    // XỬ LÝ ĐĂNG KÝ (Giữ nguyên logic gửi mail)
    public function register(Request $request) {
        $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        Auth::login($user);
        event(new Registered($user)); // Gửi email kích hoạt

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request) {
        // 1. Đăng xuất Admin
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        // 2. Đăng xuất User (Web)
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // 3. Xóa sạch Session (Quan trọng để không bị nhớ đăng nhập cũ)
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // 4. Chuyển hướng về trang đăng nhập
        return redirect()->route('login');
    }

}