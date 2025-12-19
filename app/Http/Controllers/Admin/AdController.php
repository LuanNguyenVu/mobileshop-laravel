<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use Illuminate\Support\Facades\File;

class AdController extends Controller
{
    // 1. Danh sách quảng cáo
    public function index()
    {
        $ads = Advertisement::orderBy('id', 'desc')->paginate(10);
        return view('admin.ads.index', compact('ads'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.ads.create');
    }

    // 3. Xử lý lưu mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = uniqid('ad_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ads'), $fileName);
            $imagePath = 'uploads/ads/' . $fileName; // Lưu đường dẫn
        }

        Advertisement::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'display_location' => $request->display_location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Thêm quảng cáo thành công!');
    }

    // 4. Form chỉnh sửa
    public function edit($id)
    {
        $ad = Advertisement::findOrFail($id);
        return view('admin.ads.edit', compact('ad'));
    }

    // 5. Xử lý cập nhật
    public function update(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);
        
        $request->validate([
            'title' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Logic upload ảnh mới nếu có
        if ($request->hasFile('image_file')) {
            // Xóa ảnh cũ
            if ($ad->image_path && File::exists(public_path($ad->image_path))) {
                File::delete(public_path($ad->image_path));
            }
            
            $file = $request->file('image_file');
            $fileName = uniqid('ad_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ads'), $fileName);
            $ad->image_path = 'uploads/ads/' . $fileName;
        }

        $ad->update([
            'title' => $request->title,
            'display_location' => $request->display_location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'image_path' => $ad->image_path // Cập nhật lại đường dẫn (nếu có thay đổi)
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Cập nhật thành công!');
    }

    // 6. Xóa quảng cáo
    public function destroy($id)
    {
        $ad = Advertisement::findOrFail($id);
        if ($ad->image_path && File::exists(public_path($ad->image_path))) {
            File::delete(public_path($ad->image_path));
        }
        $ad->delete();
        return redirect()->route('admin.ads.index')->with('success', 'Đã xóa quảng cáo!');
    }
}