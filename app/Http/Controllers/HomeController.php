<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Advertisement;
use App\Models\Post;
use Illuminate\Support\Facades\Cache; // <--- Thêm cái này

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy Quảng cáo (Cache 30 phút)
        $advertisements = Cache::remember('home_ads', 1800, function () {
            return Advertisement::where('status', 'Active')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->get();
        });

        // 2. Lấy Tin tức mới nhất (Cache 30 phút)
        $posts = Cache::remember('home_posts', 1800, function () {
            return Post::where('status', 'Published')
                ->latest()
                ->take(5)
                ->get();
        });

        // 3. Lấy Sản phẩm nổi bật (Cache 15 phút)
        // Chỉ select các cột cần thiết để giảm dung lượng tải
        $featuredProducts = Cache::remember('home_featured_products', 900, function () {
            return Product::with(['variants' => function($q) {
                    $q->orderBy('selling_price', 'asc'); // Sắp xếp variant để lấy giá rẻ nhất nhanh hơn
                }])
                ->where('status', 'in_stock')
                ->latest()
                ->take(10)
                ->get();
        });
        // 4. LẤY 10 SẢN PHẨM MỚI NHẤT (CHO SLIDER MỚI)
        $newProducts = Product::with('variants')
            ->where('status', 'in_stock')
            ->orderBy('created_at', 'desc') // Sắp xếp theo ngày tạo mới nhất
            ->take(10)
            ->get();
        return view('home', compact('advertisements', 'posts', 'newProducts', 'featuredProducts'));
    }

    public function about()
    {
        return view('about'); // Trang tĩnh, load cực nhanh không cần sửa
    }
}