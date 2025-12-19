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
        // Cache bài viết chi tiết 60 phút
        $post = Cache::remember('post_detail_' . $slug, 3600, function () use ($slug) {
            return Post::where('slug', $slug)
                       ->where('status', 'Published')
                       ->firstOrFail();
        });

        $latestProducts = $this->getSidebarProducts();

        return view('news.show', compact('post', 'latestProducts'));
    }
}