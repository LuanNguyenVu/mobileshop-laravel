<?php

namespace App\Http\Controllers;

use App\Models\Cart; // Nhớ use Model Cart
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductVariant;

class ProductController extends Controller
{
public function index(Request $request)
{
    // Khởi tạo query
    $query = Product::with('variants')->where('status', 'in_stock');

    // 1. Tìm kiếm từ khóa
    if ($request->filled('keyword')) {
        $query->where('product_name', 'like', '%' . $request->keyword . '%');
    }

    // 2. Lọc theo Hãng (Brand)
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }

    // 3. Xử lý Sắp xếp (Logic quan trọng)
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'price_asc': // Giá thấp đến cao
                // Dùng subquery lấy giá bán thấp nhất của biến thể để sắp xếp
                $query->addSelect(['min_price' => ProductVariant::select('selling_price')
                    ->whereColumn('product_id', 'products.id')
                    ->orderBy('selling_price', 'asc')
                    ->limit(1)
                ])->orderBy('min_price', 'asc');
                break;

            case 'price_desc': // Giá cao đến thấp
                $query->addSelect(['min_price' => ProductVariant::select('selling_price')
                    ->whereColumn('product_id', 'products.id')
                    ->orderBy('selling_price', 'asc')
                    ->limit(1)
                ])->orderBy('min_price', 'desc');
                break;

            case 'rating_desc': // Đánh giá cao
                $query->orderBy('rating', 'desc');
                break;

            case 'default':
            default:
                $query->latest();
                break;
        }
    } else {
        $query->latest(); // Mặc định là mới nhất
    }

    // Lấy banner quảng cáo (Code cũ của bạn)
    $headerAds = \App\Models\Advertisement::where('status', 'Active')
        ->where('display_location', '!=', 'Trang Chủ') 
        ->orderBy('created_at', 'desc')
        ->take(2)
        ->get();

    // Phân trang
    $products = $query->paginate(15)->withQueryString();

    // Các biến phụ trợ cho View
    $brands = ['Apple', 'Samsung', 'OPPO', 'Xiaomi', 'Sony']; 
    $os_options = ['Android', 'iOS', 'Khác']; 
    $product_types = ['Điện thoại', 'Tablet', 'Phụ kiện'];
    $current_brand = $request->brand ?? 'Tất cả sản phẩm';

    return view('products.index', compact('products', 'brands', 'os_options', 'product_types', 'current_brand', 'headerAds'));
}

    public function show($id)
    {
        $product = Product::with(['variants', 'reviews.user'])->findOrFail($id);
        
        // 1. Lấy số lượng từng biến thể trong giỏ hàng của User (Nếu đã đăng nhập)
        $cartQuantities = [];
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                // Tạo mảng dạng: [variant_id => quantity, 101 => 5, 102 => 2 ...]
                $cartQuantities = $cart->items->pluck('quantity', 'product_variant_id')->toArray();
            }
        }

        $relatedProducts = Product::where('id', '!=', $id)
            ->where('brand', $product->brand)
            ->with('variants')
            ->take(4)
            ->get();

        // Truyền thêm biến $cartQuantities sang View
        return view('products.detail', compact('product', 'relatedProducts', 'cartQuantities'));
    }
}