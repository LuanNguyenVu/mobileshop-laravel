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

        $cart = $this->getCart();
        $cartItems = $cart ? $cart->items()->with('variant.product')->get() : collect([]);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        // Tính tổng tiền
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $variant = $item->variant;
            $price = ($variant->promotional_price > 0) ? $variant->promotional_price : $variant->selling_price;
            $totalAmount += $item->quantity * $price;
        }

        $shippingFee = 30000; // Phí ship cố định (hoặc logic tính riêng)
        $finalTotal = $totalAmount + $shippingFee;

        return view('checkout.index', compact('cartItems', 'totalAmount', 'shippingFee', 'finalTotal'));
    }

// --- 2. XỬ LÝ ĐẶT HÀNG (LOGIC MỚI - CHẶT CHẼ HƠN) ---
    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = $this->getCart();
        $cartItems = $cart->items()->with('variant.product')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống!');
        }

        // Bắt đầu Transaction (Nếu có 1 lỗi nhỏ, hủy toàn bộ thao tác để tránh sai lệch tiền/hàng)
        DB::beginTransaction();
        try {
            
            // --- BƯỚC 1: KIỂM TRA TỒN KHO LẦN CUỐI (QUAN TRỌNG NHẤT) ---
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                // Dùng lockForUpdate để khóa dòng dữ liệu này lại, không cho ai mua tranh trong lúc đang xử lý
                $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);

                if (!$variant) {
                    throw new \Exception("Sản phẩm trong giỏ hàng không còn tồn tại.");
                }

                // Nếu số lượng trong giỏ > Số lượng thực tế trong kho
                if ($item->quantity > $variant->quantity) {
                    throw new \Exception("Sản phẩm '" . $variant->product->product_name . " - " . $variant->color . "' hiện chỉ còn " . $variant->quantity . " cái. Vui lòng cập nhật lại giỏ hàng.");
                }

                // Tính tiền luôn tại đây
                $price = ($variant->promotional_price > 0 && $variant->promotional_price < $variant->selling_price) 
                            ? $variant->promotional_price 
                            : $variant->selling_price;
                $totalAmount += $item->quantity * $price;
            }

            $shippingFee = 30000;
            $finalTotal = $totalAmount + $shippingFee;

            // --- BƯỚC 2: TẠO ĐƠN HÀNG ---
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

            // --- BƯỚC 3: TẠO CHI TIẾT & TRỪ KHO ---
            foreach ($cartItems as $item) {
                $variant = $item->variant; // Lúc này đã an toàn vì đã check ở Bước 1
                
                // Lấy giá tại thời điểm mua (đề phòng admin vừa đổi giá)
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
                
                // TRỪ KHO NGAY LẬP TỨC
                $variant->decrement('quantity', $item->quantity);
            }

            // --- BƯỚC 4: XÓA GIỎ HÀNG ---
            $cart->items()->delete();

            DB::commit(); // Xác nhận mọi thứ ok -> Lưu xuống DB

            return redirect()->route('home')->with('success', 'Đặt hàng thành công! Mã đơn: ' . $order->order_code);

        } catch (\Exception $e) {
            DB::rollBack(); // Có lỗi -> Hoàn tác tất cả (không tạo đơn, không trừ kho)
            return back()->with('error', 'Lỗi đặt hàng: ' . $e->getMessage());
        }
    }
}
