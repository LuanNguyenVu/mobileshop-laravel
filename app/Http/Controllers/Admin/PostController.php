<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    // 1. Danh sách tin tức
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.posts.create');
    }

    // 3. Xử lý lưu bài viết mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:posts,title',
            'content' => 'required',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tạo slug từ tiêu đề
        $slug = Str::slug($request->title);

        // Upload ảnh
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $fileName = uniqid('post_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $fileName);
            $thumbnailPath = 'uploads/posts/' . $fileName;
        }

        Post::create([
            'title' => $request->input('title'),
            'slug' => $slug,
            'content' => $request->input('content'),
            'thumbnail_path' => $thumbnailPath,
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công!');
    }

    // 4. Form chỉnh sửa
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    // 5. Xử lý cập nhật bài viết
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|unique:posts,title,' . $post->id,
            'content' => 'required',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'content' => $request->input('content'),
            'status' => $request->input('status'),
        ];

        // Nếu có upload ảnh mới
        if ($request->hasFile('thumbnail_file')) {
            // Xóa ảnh cũ
            if ($post->thumbnail_path && File::exists(public_path($post->thumbnail_path))) {
                File::delete(public_path($post->thumbnail_path));
            }

            $file = $request->file('thumbnail_file');
            $fileName = uniqid('post_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $fileName);
            $data['thumbnail_path'] = 'uploads/posts/' . $fileName;
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    // 6. Xóa bài viết
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if ($post->thumbnail_path && File::exists(public_path($post->thumbnail_path))) {
            File::delete(public_path($post->thumbnail_path));
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết!');
    }
}
