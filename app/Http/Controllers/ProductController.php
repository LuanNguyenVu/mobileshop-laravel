<?php

namespace App\Http\Controllers;

use App\Models\Cart; // Nhớ use Model Cart
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Eager Load variants để tính giá nhanh
        $query = Product::with('variants')->where('status', 'in_stock');

        // Tìm kiếm & Lọc (Giữ nguyên logic của bạn, nó đã ổn)
        if ($request->filled('keyword')) {
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }
        if ($request->filled('os')) {
            $query->where('os', $request->os);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sắp xếp
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'rating_desc':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'default':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(15)->withQueryString();

        // Data tĩnh cho bộ lọc -> Không cần query DB, hardcode cho nhanh
        $brands = ['Apple', 'Samsung', 'OPPO', 'Xiaomi', 'Sony', 'Huawei'];
        $os_options = ['Android', 'iOS', 'Khác'];
        $product_types = ['Điện thoại', 'Tablet', 'Phụ kiện'];
        $current_brand = $request->brand ?? 'Tất cả';

        return view('products.index', compact('products', 'brands', 'os_options', 'product_types', 'current_brand'));
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