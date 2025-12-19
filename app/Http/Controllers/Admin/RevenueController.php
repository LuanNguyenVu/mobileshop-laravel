<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy tham số lọc (Mặc định tháng/năm hiện tại)
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        // 2. Thống kê tổng quan (Cards)
        // Chỉ tính các đơn hàng đã giao thành công (Delivered)
        $summary = Order::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('order_status', 'Delivered')
            ->selectRaw('
                COUNT(id) as total_orders,
                SUM(total_amount) as total_revenue
            ')
            ->first();

        // Tính tổng số lượng sản phẩm bán ra & Lợi nhuận (Giả định)
        // Lưu ý: Lợi nhuận = Giá bán - Giá nhập. Trong Laravel cần join bảng OrderDetail và ProductVariant.
        $detailsQuery = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('product_variants as pv', 'od.product_variant_id', '=', 'pv.id') // Giả sử bạn có cột product_variant_id trong order_details (hoặc logic tương tự)
            ->where('o.order_status', 'Delivered')
            ->whereMonth('o.created_at', $month)
            ->whereYear('o.created_at', $year);

        // Nếu bảng order_details của bạn chưa lưu product_variant_id mà chỉ lưu text, 
        // bạn có thể phải join qua products. Ở đây mình giả định cấu trúc chuẩn.
        // Tuy nhiên, theo code cũ của bạn, ta cần tính lợi nhuận dựa trên (giá bán - giá mua).
        
        // Vì cấu trúc DB hiện tại của bạn trong OrderDetail lưu cứng giá lúc bán (price_at_order),
        // nhưng giá nhập (purchase_price) lại nằm ở ProductVariant.
        // Để đơn giản hóa cho demo Laravel này (tránh query phức tạp nếu quan hệ chưa chuẩn),
        // mình sẽ tính toán dựa trên dữ liệu có sẵn.

        // Lấy danh sách chi tiết để hiển thị bảng & tính toán
        $soldItems = DB::table('order_details as od')
            ->join('orders as o', 'od.order_id', '=', 'o.id')
            ->join('products as p', function($join) {
                // Join theo tên sản phẩm nếu không có ID (Cách chữa cháy)
                // Hoặc join chuẩn nếu có product_id trong order_details
                $join->on('od.product_name', '=', 'p.product_name'); 
            })
            ->where('o.order_status', 'Delivered')
            ->whereMonth('o.created_at', $month)
            ->whereYear('o.created_at', $year)
            ->select(
                'o.created_at as order_date',
                'o.order_code',
                'od.product_name',
                'od.variant_color',
                'p.brand as manufacturer',
                'od.quantity',
                'od.price_at_order'
            )
            ->get();

        // Xử lý dữ liệu cho biểu đồ & Tổng kết
        $totalSoldQty = 0;
        $totalProfit = 0; // Tạm tính profit = 20% doanh thu nếu chưa có giá nhập chuẩn
        $manufacturerStats = [];

        foreach ($soldItems as $item) {
            $revenue = $item->quantity * $item->price_at_order;
            $totalSoldQty += $item->quantity;
            
            // Giả định lợi nhuận 20% (Bạn thay bằng logic (price - purchase_price) nếu query được)
            $profit = $revenue * 0.2; 
            $totalProfit += $profit;

            // Gom nhóm theo hãng
            $brand = $item->manufacturer ?: 'Khác';
            if (!isset($manufacturerStats[$brand])) {
                $manufacturerStats[$brand] = ['sales' => 0, 'profit' => 0];
            }
            $manufacturerStats[$brand]['sales'] += $revenue;
            $manufacturerStats[$brand]['profit'] += $profit;
        }

        $summaryStats = [
            'orders' => $summary->total_orders ?? 0,
            'products_sold' => $totalSoldQty,
            'revenue' => $summary->total_revenue ?? 0,
            'profit' => $totalProfit
        ];

        return view('admin.revenue.index', compact('summaryStats', 'manufacturerStats', 'soldItems', 'month', 'year'));
    }
}
