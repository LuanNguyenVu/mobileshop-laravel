<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Thay vì get() lấy hết, ta dùng paginate(10)
        // Nếu khách mua 100 đơn, load 1 lần sẽ rất chậm
        $orders = Order::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(10); 

        return view('orders.index', compact('orders'));
    }

    public function show($orderCode)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = Order::with(['orderDetails', 'orderDetails.productVariant']) // Load sâu thêm biến thể nếu cần
                      ->where('order_code', $orderCode)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        return view('orders.show', compact('order'));
    }
}