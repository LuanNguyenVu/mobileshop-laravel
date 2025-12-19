<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

class CartController extends Controller
{
    // Hàm phụ trợ: Lấy hoặc tạo giỏ hàng cho User hiện tại
    protected function getCart()
    {
        // Nếu chưa đăng nhập -> trả về null
        if (!Auth::check()) {
            return null;
        }

        // Tìm giỏ hàng của user, nếu chưa có thì tạo mới
        return Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);
    }

    // --- 1. XEM GIỎ HÀNG (Hàm index bị thiếu gây ra lỗi) ---
    public function index()
    {
        $cart = $this->getCart();
        
        // Nếu có giỏ hàng thì lấy sản phẩm, nếu không thì trả về rỗng
        $cartItems = $cart ? $cart->items()->with('variant.product')->get() : collect([]);
        
        // Tính tổng tiền
        $totalAmount = 0;
        foreach($cartItems as $item) {
            $variant = $item->variant;
            // Kiểm tra xem biến thể còn tồn tại không
            if($variant) {
                $price = ($variant->promotional_price > 0 && $variant->promotional_price < $variant->selling_price)
                            ? $variant->promotional_price 
                            : $variant->selling_price;
                $totalAmount += $price * $item->quantity;
            }
        }

        return view('cart.index', compact('cartItems', 'totalAmount'));
    }

    // --- 2. THÊM VÀO GIỎ HÀNG (LOGIC TỰ ĐỘNG CẬP NHẬT SỐ LƯỢNG) ---
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để mua hàng!']);
        }

        $variantId = $request->input('variant_id');
        $quantity = (int)$request->input('quantity');

        if (!$variantId || $quantity <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
        }

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại!']);
        }

        $cart = $this->getCart();
        $cartItem = $cart->items()->where('product_variant_id', $variantId)->first();

        // 1. Tính toán số lượng có thể thêm
        $currentInCart = $cartItem ? $cartItem->quantity : 0;
        $maxAddable = $variant->quantity - $currentInCart;

        // Trường hợp 1: Trong giỏ đã full kho rồi
        if ($maxAddable <= 0) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Bạn đã có ' . $currentInCart . ' sản phẩm này trong giỏ (Max kho: ' . $variant->quantity . '). Không thể thêm nữa.'
            ]);
        }

        // Trường hợp 2: Số lượng muốn thêm lớn hơn số lượng CHO PHÉP thêm
        $realQtyToAdd = $quantity;
        $warningMsg = '';

        if ($quantity > $maxAddable) {
            $realQtyToAdd = $maxAddable; // Chỉ thêm số lượng còn thiếu
            $warningMsg = "\n(Lưu ý: Kho chỉ còn đủ để thêm $realQtyToAdd sản phẩm. Hệ thống đã tự động điều chỉnh số lượng giúp bạn).";
        }

        // 2. Thực hiện Lưu/Update
        if ($cartItem) {
            $cartItem->quantity += $realQtyToAdd;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_variant_id' => $variantId,
                'quantity' => $realQtyToAdd
            ]);
        }

        return response()->json([
            'status' => 'success',
            // Thông báo thông minh: Đã thêm bao nhiêu và cảnh báo nếu bị cắt giảm
            'message' => "Đã thêm $realQtyToAdd sản phẩm vào giỏ hàng!" . $warningMsg,
            'cart_count' => $cart->items->sum('quantity')
        ]);
    }

    // --- 3. MUA NGAY ---
    public function buyNow(Request $request)
    {
        $response = $this->add($request);
        $data = $response->getData();

        if ($data->status === 'success') {
            return response()->json([
                'status' => 'success',
                'redirect' => route('checkout') 
            ]);
        }
        return $response;
    }

    // --- 4. CẬP NHẬT SỐ LƯỢNG ---
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::find($request->id);
        
        if ($cartItem) {
            $cartItem->update(['quantity' => $request->quantity]);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // --- 5. XÓA SẢN PHẨM ---
    public function remove($id)
    {
        $cartItem = CartItem::find($id);
        if ($cartItem) {
            $cartItem->delete();
        }
        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm!');
    }
}