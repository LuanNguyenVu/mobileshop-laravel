<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    // 1. Hiển thị danh sách tài khoản
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // 2. Xem chi tiết tài khoản
    public function show($id)
    {
        // Gộp chung Eager Loading vào một câu lệnh duy nhất để lấy cả đơn hàng và bình luận kèm sản phẩm
        $user = User::with(['orders', 'reviews.product'])->findOrFail($id);

        // Không cần khai báo $comments = [] nữa vì View sẽ dùng $user->reviews
        return view('admin.users.show', compact('user'));
    }

    // 3. Xóa tài khoản
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Logic xóa: Chỉ cho xóa nếu chưa xác thực email (Inactive theo yêu cầu trước đó của bạn)
        if ($user->email_verified_at !== null) {
            return back()->with('error', 'Không thể xóa tài khoản đã kích hoạt (Active).');
        }

        if ($user->avatar_path && File::exists(public_path($user->avatar_path))) {
            File::delete(public_path($user->avatar_path));
        }

        $user->delete();
        return back()->with('success', 'Đã xóa tài khoản thành công!');
    }
}