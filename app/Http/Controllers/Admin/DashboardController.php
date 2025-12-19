<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê tổng quan
        // Chỉ tính doanh thu các đơn hàng đã giao thành công (giả sử trạng thái là 'Delivered')
        // Nếu bạn muốn tính tất cả, bỏ đoạn where(...) đi
        $totalRevenue = Order::where('order_status', 'Delivered')->sum('total_amount');
        
        $totalOrders = Order::count();
        $totalCustomers = User::count(); // Hoặc lọc theo role nếu có: User::where('role', 'customer')->count()
        $totalProducts = Product::count();

        // 2. Lấy 5 đơn hàng mới nhất
        $recentOrders = Order::latest()->take(5)->get();

        // 3. Lấy thông báo (Ví dụ: Khách hàng mới & Sản phẩm sắp hết hàng)
        $newUsers = User::latest()->take(3)->get();
        
        // Lấy các biến thể sản phẩm có số lượng < 10
        $lowStockProducts = ProductVariant::with('product')
                                        ->where('quantity', '<', 10)
                                        ->take(3)
                                        ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 
            'totalOrders', 
            'totalCustomers', 
            'totalProducts',
            'recentOrders',
            'newUsers',
            'lowStockProducts'
        ));
    }
}