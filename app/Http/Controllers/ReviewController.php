<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Kiểm tra xem người dùng đã mua sản phẩm chưa (Tùy chọn nâng cao sau này)
        // Hiện tại cho phép đánh giá thoải mái nếu đã đăng nhập

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Cập nhật lại điểm đánh giá trung bình cho Product
        $product = Product::find($productId);
        $avgRating = $product->reviews()->avg('rating');
        $product->update(['rating' => round($avgRating, 1)]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}