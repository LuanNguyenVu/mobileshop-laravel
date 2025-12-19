@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code)

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/orders.css') }}">
@endpush

@section('content')
<div class="container order-page-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title" style="border:none; margin:0; padding:0;">
            Chi Tiết Đơn Hàng #{{ $order->order_code }}
        </h2>
        <div class="order-meta">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</div>
    </div>
    
    <div class="info-grid">
        <div class="info-card">
            <h3><i class="fas fa-user-circle"></i> Thông Tin Tài Khoản</h3>
            <div class="info-row"><strong>Tên:</strong> <span>{{ Auth::user()->username }}</span></div>
            <div class="info-row"><strong>Email:</strong> <span>{{ Auth::user()->email }}</span></div>
            <div class="info-row"><strong>SĐT:</strong> <span>{{ Auth::user()->phone ?? 'Chưa cập nhật' }}</span></div>
        </div>

        <div class="info-card">
            <h3><i class="fas fa-map-marker-alt"></i> Thông Tin Giao Hàng</h3>
            <div class="info-row"><strong>Người nhận:</strong> <span>{{ $order->receiver_name }}</span></div>
            <div class="info-row"><strong>SĐT:</strong> <span>{{ $order->receiver_phone }}</span></div>
            <div class="info-row"><strong>Địa chỉ:</strong> <span>{{ $order->receiver_address }}</span></div>
            @if($order->note)
                <div class="info-row" style="margin-top:10px; padding-top:10px; border-top:1px dashed #eee;">
                    <strong>Ghi chú:</strong> <em style="color: #d70018;">{{ $order->note }}</em>
                </div>
            @endif
        </div>
    </div>

    <div class="payment-status-box">
        <div>
            <span style="color:#666;">Phương thức thanh toán:</span>
            <strong style="margin-left: 5px;">{{ $order->payment_method }}</strong>
        </div>
        <div>
            <span style="color:#666;">Trạng thái đơn hàng:</span>
            @php
                $statusClass = match($order->order_status) {
                    'Pending' => 'status-pending',
                    'Processing' => 'status-processing',
                    'Shipped' => 'status-shipped',
                    'Delivered' => 'status-delivered',
                    'Cancelled' => 'status-cancelled',
                    default => 'status-pending'
                };
                 $statusLabel = match($order->order_status) {
                    'Pending' => 'Chờ xử lý',
                    'Processing' => 'Đang xử lý',
                    'Shipped' => 'Đang giao',
                    'Delivered' => 'Hoàn thành',
                    'Cancelled' => 'Đã hủy',
                    default => $order->order_status
                };
            @endphp
            <span class="status-badge {{ $statusClass }}" style="margin-left: 10px;">{{ $statusLabel }}</span>
        </div>
    </div>

    <h3 style="font-size: 18px; margin-bottom: 15px; font-weight: 700;">Danh Sách Sản Phẩm</h3>
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th class="text-center" width="5%">STT</th>
                    <th width="40%">Sản Phẩm</th>
                    <th width="15%">Phân Loại</th>
                    <th width="10%" class="text-center">SL</th>
                    <th width="15%" class="text-right">Đơn Giá</th>
                    <th width="15%" class="text-right">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $index => $item)
                    @php 
                        $subtotal = $item->quantity * $item->price_at_order; 
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: #333;">{{ $item->product_name }}</td>
                        <td>
                            <span style="background: #f0f0f0; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $item->variant_color }}
                            </span>
                        </td>
                        <td class="text-center">x{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price_at_order, 0, ',', '.') }}đ</td>
                        <td class="text-right price-text">{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="5" class="text-right">TỔNG CỘNG THANH TOÁN:</td>
                    <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px;">
        <a href="{{ route('order_list') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection