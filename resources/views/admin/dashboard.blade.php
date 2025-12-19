@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h3>Tổng Quan Kinh Doanh</h3>
    <p class="text-muted">Thống kê hoạt động thực tế của cửa hàng MobileShop</p>
</div>

{{-- 1. THỐNG KÊ SỐ LIỆU --}}
<div class="row">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon blue"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-details">
                <h5>Doanh Thu</h5>
                {{-- Format tiền tệ --}}
                <h2>{{ number_format($totalRevenue, 0, ',', '.') }}₫</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon orange"><i class="fas fa-shopping-bag"></i></div>
            <div class="stat-details">
                <h5>Đơn Hàng</h5>
                <h2>{{ number_format($totalOrders) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon green"><i class="fas fa-users"></i></div>
            <div class="stat-details">
                <h5>Khách Hàng</h5>
                <h2>{{ number_format($totalCustomers) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon red"><i class="fas fa-box"></i></div>
            <div class="stat-details">
                <h5>Sản Phẩm</h5>
                <h2>{{ number_format($totalProducts) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    {{-- 2. BẢNG ĐƠN HÀNG MỚI NHẤT --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Đơn Hàng Mới Nhất</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    {{-- Link đến chi tiết đơn hàng (nếu có route này) --}}
                                    <a href="#" class="fw-bold text-dark">#{{ $order->order_code }}</a>
                                </td>
                                <td>{{ $order->receiver_name }}</td>
                                <td class="fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                                <td>
                                    @php
                                        $badges = [
                                            'Pending' => 'bg-warning',
                                            'Processing' => 'bg-info',
                                            'Shipped' => 'bg-primary',
                                            'Delivered' => 'bg-success',
                                            'Cancelled' => 'bg-danger',
                                        ];
                                        $badgeClass = $badges[$order->order_status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 3. THÔNG BÁO / SIDEBAR --}}
    <div class="col-md-4">
        <div class="card p-3">
            <h5 class="fw-bold mb-3">Thông Báo Hệ Thống</h5>
            <ul class="list-group list-group-flush">
                
                {{-- Cảnh báo kho hàng thấp --}}
                @foreach($lowStockProducts as $variant)
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle text-danger me-3 fs-4"></i>
                        <div>
                            <strong>Sắp hết hàng</strong><br>
                            <small class="text-muted">
                                {{ $variant->product->product_name }} ({{ $variant->color }}) 
                                <br> Chỉ còn: <b class="text-danger">{{ $variant->quantity }}</b> cái
                            </small>
                        </div>
                    </li>
                @endforeach

                {{-- Khách hàng mới --}}
                @foreach($newUsers as $user)
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-user-plus text-primary me-3 fs-4"></i>
                        <div>
                            <strong>Thành viên mới</strong><br>
                            <small class="text-muted">{{ $user->username }} vừa đăng ký</small>
                        </div>
                    </li>
                @endforeach

                @if($lowStockProducts->isEmpty() && $newUsers->isEmpty())
                    <li class="list-group-item text-center text-muted">Không có thông báo mới.</li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection