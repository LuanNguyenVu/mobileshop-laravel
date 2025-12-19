<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;

class OrderController extends Controller
{
    // 1. Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhere('receiver_phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái (nếu muốn)
        if ($request->has('status') && $request->status != '') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    // 2. Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['orderDetails', 'user'])->findOrFail($id);
        
        // Tính tổng tiền chi tiết (để so sánh hoặc hiển thị)
        $subtotal = 0;
        foreach($order->orderDetails as $detail) {
            $subtotal += $detail->quantity * $detail->price_at_order;
        }

        return view('admin.orders.show', compact('order', 'subtotal'));
    }

    // 3. Cập nhật trạng thái đơn hàng
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'order_status' => 'required|in:Pending,Processing,Shipped,Delivered,Cancelled'
        ]);

        $order->update([
            'order_status' => $request->order_status
        ]);

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    // 4. Xóa đơn hàng
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        // Xóa chi tiết trước (nếu không set cascade ở DB)
        $order->orderDetails()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Đã xóa đơn hàng!');
    }
}
