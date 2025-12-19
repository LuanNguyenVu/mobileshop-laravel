<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AccountController extends Controller
{
    // 1. Xem hồ sơ
    public function index()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    // 2. Form chỉnh sửa
    public function edit()
    {
        $user = Auth::user();
        return view('account.edit', compact('user'));
    }

    // 3. Xử lý cập nhật thông tin
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('account.profile')->with('success', 'Cập nhật thông tin thành công!');
    }

    // 4. Xử lý Upload Avatar
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Tối đa 2MB
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            
            // Đặt tên file ngẫu nhiên để tránh trùng
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Lưu vào thư mục public/uploads/avatars
            $destinationPath = public_path('uploads/avatars');
            
            // Xóa ảnh cũ nếu không phải ảnh mặc định
            if ($user->avatar_path && File::exists(public_path($user->avatar_path))) {
                // Kiểm tra xem có phải ảnh mặc định không trước khi xóa (tùy logic)
                File::delete(public_path($user->avatar_path));
            }

            // Di chuyển file mới
            $file->move($destinationPath, $fileName);

            // Cập nhật đường dẫn vào CSDL
            // Lưu đường dẫn tương đối để dùng với asset()
            $user->avatar_path = 'uploads/avatars/' . $fileName;
            $user->save();

            return back()->with('success', 'Đã cập nhật ảnh đại diện!');
        }

        return back()->with('error', 'Vui lòng chọn file ảnh hợp lệ.');
    }
}