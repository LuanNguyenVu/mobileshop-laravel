<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // Hàm lấy giỏ hàng (giống bên CartController - có thể tách ra Service nếu muốn tái sử dụng)
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        return null; // Bắt buộc đăng nhập mới được thanh toán (đơn giản hóa)
    }

    // 1. Hiển thị trang Thanh Toán
public function index()
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán.');
    }

    $cartItems = collect([]); // Khởi tạo Collection rỗng
    $totalAmount = 0;
    $isBuyNow = false; // Biến cờ để nhận biết đang mua ngay hay mua thường

    // --- TRƯỜNG HỢP 1: CÓ SESSION MUA NGAY ---
    if (session()->has('buy_now_data')) {
        $sessionData = session()->get('buy_now_data');
        $variant = ProductVariant::with('product')->find($sessionData['variant_id']);

        if ($variant) {
            // Giả lập một đối tượng giống CartItem để View không bị lỗi
            $fakeItem = new \stdClass();
            $fakeItem->product_variant_id = $variant->id;
            $fakeItem->quantity = $sessionData['quantity'];
            $fakeItem->variant = $variant; // Gắn quan hệ variant vào

            $cartItems->push($fakeItem);
            $isBuyNow = true;
        }
    } 
    // --- TRƯỜNG HỢP 2: LẤY TỪ GIỎ HÀNG (DATABASE) ---
    else {
        $cart = $this->getCart();
        if ($cart) {
            $cartItems = $cart->items()->with('variant.product')->get();
        }
    }

    // Kiểm tra rỗng
    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào để thanh toán!');
    }

    // Tính tổng tiền (Dùng chung cho cả 2 trường hợp)
    foreach ($cartItems as $item) {
        $variant = $item->variant;
        $price = ($variant->promotional_price > 0 && $variant->promotional_price < $variant->selling_price) 
                    ? $variant->promotional_price 
                    : $variant->selling_price;
        $totalAmount += $item->quantity * $price;
    }

    $shippingFee = 30000;
    $finalTotal = $totalAmount + $shippingFee;

    // Truyền biến $isBuyNow sang view để hiển thị thông báo nếu cần
    return view('checkout.index', compact('cartItems', 'totalAmount', 'shippingFee', 'finalTotal', 'isBuyNow'));
}

public function placeOrder(Request $request)
{
    $request->validate([
        'name' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'payment_method' => 'required'
    ]);

    DB::beginTransaction();
    try {
        $itemsToProcess = [];
        $isBuyNowOrder = false;

        // 1. XÁC ĐỊNH NGUỒN DỮ LIỆU (Session hay Database)
        if (session()->has('buy_now_data')) {
            // Lấy từ Session
            $sessionData = session()->get('buy_now_data');
            $variant = ProductVariant::lockForUpdate()->find($sessionData['variant_id']);
            
            // Validate lại tồn kho
            if (!$variant || $sessionData['quantity'] > $variant->quantity) {
                throw new \Exception("Sản phẩm này vừa hết hàng hoặc không đủ số lượng.");
            }

            // Tạo item giả lập
            $fakeItem = new \stdClass();
            $fakeItem->variant = $variant;
            $fakeItem->quantity = $sessionData['quantity'];
            $fakeItem->product_variant_id = $variant->id;
            
            $itemsToProcess[] = $fakeItem;
            $isBuyNowOrder = true;

        } else {
            // Lấy từ Giỏ hàng DB
            $cart = $this->getCart();
            $itemsFromCart = $cart->items()->with('variant.product')->get();
            
            if ($itemsFromCart->isEmpty()) {
                return back()->with('error', 'Giỏ hàng trống!');
            }

            foreach($itemsFromCart as $cartItem) {
                // Lock để check kho
                $variant = ProductVariant::lockForUpdate()->find($cartItem->product_variant_id);
                if ($cartItem->quantity > $variant->quantity) {
                    throw new \Exception("Sản phẩm {$variant->product->product_name} không đủ hàng.");
                }
                $cartItem->variant = $variant; // Gán lại variant đã lock
                $itemsToProcess[] = $cartItem;
            }
        }

        // 2. TÍNH TỔNG TIỀN LẠI (Backend phải tính, ko tin tưởng Frontend)
        $totalAmount = 0;
        foreach ($itemsToProcess as $item) {
            $variant = $item->variant;
            $price = ($variant->promotional_price > 0 && $variant->promotional_price < $variant->selling_price) 
                        ? $variant->promotional_price 
                        : $variant->selling_price;
            $totalAmount += $item->quantity * $price;
        }
        $finalTotal = $totalAmount + 30000; // + Phí ship

        // 3. TẠO ORDER
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_code' => 'ORD-' . strtoupper(Str::random(10)),
            'receiver_name' => $request->name,
            'receiver_phone' => $request->phone,
            'receiver_email' => $request->email ?? Auth::user()->email,
            'receiver_address' => $request->address,
            'note' => $request->notes,
            'total_amount' => $finalTotal,
            'payment_method' => $request->payment_method,
            'order_status' => 'Pending'
        ]);

        // 4. TẠO ORDER DETAILS & TRỪ KHO
        foreach ($itemsToProcess as $item) {
            $variant = $item->variant;
            $price = ($variant->promotional_price > 0 && $variant->promotional_price < $variant->selling_price) 
                        ? $variant->promotional_price 
                        : $variant->selling_price;

            OrderDetail::create([
                'order_id' => $order->id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->product_name,
                'variant_color' => $variant->color,
                'quantity' => $item->quantity,
                'price_at_order' => $price
            ]);

            $variantToUpdate = ProductVariant::find($item->product_variant_id);
                
                if ($variantToUpdate) {
                    $variantToUpdate->decrement('quantity', $item->quantity);
                }
        }

        // 5. DỌN DẸP (Quan trọng)
        if ($isBuyNowOrder) {
            // Nếu là mua ngay -> Chỉ xóa session, KHÔNG xóa giỏ hàng
            session()->forget('buy_now_data');
        } else {
            // Nếu mua từ giỏ -> Xóa giỏ hàng
            $this->getCart()->items()->delete();
        }

        DB::commit();
        return redirect()->route('home')->with('success', 'Đặt hàng thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Lỗi: ' . $e->getMessage());
    }
    return redirect()->route('home')->with('success', 'Đặt hàng thành công! Mã đơn: ' . $order->order_code);
}
}
