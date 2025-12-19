@extends('layouts.app')

@section('title', 'Đơn Hàng Của Tôi')

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/orders.css') }}">
@endpush

@section('content')
<div class="container order-page-container">
    <h2 class="page-title">
        Đơn Hàng Của Tôi
        <span>{{ $orders->count() }} đơn hàng</span>
    </h2>
    
    @if($orders->isEmpty())
        <div class="empty-order-state">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" alt="Empty">
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="{{ route('products.index') }}" class="btn-primary-action">Mua sắm ngay</a>
        </div>
    @else
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">STT</th>
                        <th width="15%">Mã Đơn Hàng</th>
                        <th width="20%">Ngày Đặt</th>
                        <th width="20%">Thanh Toán</th>
                        <th width="15%" class="text-right">Tổng Tiền</th>
                        <th width="15%" class="text-center">Trạng Thái</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $index => $order)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order->order_code) }}" class="order-code-link">
                                #{{ $order->order_code }}
                            </a>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->payment_method }}</td>
                        <td class="text-right price-text">
                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                        </td>
                        <td class="text-center">
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
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('orders.show', $order->order_code) }}" style="color: #666;">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection