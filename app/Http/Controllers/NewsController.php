<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    // Cache Sidebar sản phẩm 30 phút vì nó ít thay đổi
    private function getSidebarProducts()
    {
        return Cache::remember('sidebar_products', 1800, function () {
            return Product::with('variants')
                ->where('status', 'in_stock')
                ->latest()
                ->take(5)
                ->get();
        });
    }

    public function index()
    {
        $posts = Post::where('status', 'Published')
                     ->latest()
                     ->paginate(6); // Paginate đã tối ưu sẵn

        $latestProducts = $this->getSidebarProducts();

        return view('news.index', compact('posts', 'latestProducts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        // Lấy bài viết liên quan (Trừ bài hiện tại, lấy 3 bài mới nhất)
        $relatedPosts = Post::where('id', '!=', $post->id)
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();

        // Lấy sản phẩm cho sidebar (giữ nguyên logic cũ của bạn)
        $latestProducts = Product::latest()->take(5)->get(); 

        return view('news.show', compact('post', 'relatedPosts', 'latestProducts'));
    }
}