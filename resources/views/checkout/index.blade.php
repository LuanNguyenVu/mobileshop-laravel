@extends('layouts.app')

@section('title', 'Thanh Toán')
@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/checkout.css') }}">
@endpush
@section('content')
<div class="container checkout-page">
    <div class="checkout-wrapper">
        
        <form action="{{ route('checkout.placeOrder') }}" method="POST" class="checkout-form">
            @csrf <div class="left-column">
                
                <h2>Thông Tin Mua Hàng</h2>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    {{-- Lấy email từ User đang đăng nhập --}}
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" readonly class="form-control" style="background-color: #eee;">
                </div>
                
                <div class="form-group">
                    <label for="name">Họ và Tên</label>
                    <input type="text" id="name" name="name" value="{{ Auth::user()->username }}" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="phone">Số Điện Thoại</label>
                    <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone }}" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="address">Địa Chỉ</label>
                    <textarea id="address" name="address" required class="form-control" rows="3">{{ Auth::user()->address }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="notes">Ghi Chú (Tùy chọn)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
                </div>
                
                <hr>
                
                <h2>Phương Thức Thanh Toán</h2>
                <div class="payment-method">
                    <label class="radio-label">
                        <input type="radio" name="payment_method" value="COD" checked>
                        Thanh Toán Khi Nhận Hàng (COD)
                        <span class="sub-text">Bạn chỉ phải thanh toán khi nhận hàng.</span>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="payment_method" value="ONLINE">
                        Thanh Toán Online (VNPAY / MOMO)
                        <span class="sub-text">Chức năng đang phát triển (Mặc định sẽ là COD).</span>
                    </label>
                </div>
            </div>

            <div class="right-column">
                <h2>Đơn Hàng ({{ count($cartItems) }} Sản Phẩm)</h2>
                
                <div class="order-list">
                    @foreach ($cartItems as $item)
                        @php
                            $variant = $item->variant;
                            $product = $variant->product;
                            $price = ($variant->promotional_price > 0) ? $variant->promotional_price : $variant->selling_price;
                        @endphp
                        <div class="order-item">
                            <img src="{{ asset('uploads/' . $product->product_image) }}" alt="{{ $product->product_name }}">
                            <div class="item-details">
                                <p class="item-name">{{ $product->product_name }} - {{ $variant->color }}</p>
                                <span class="item-qty">x{{ $item->quantity }}</span>
                                <span class="item-price">{{ number_format($item->quantity * $price, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <hr>

                <div class="summary-line">
                    <span>Tạm Tính:</span>
                    <span class="amount">{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                </div>
                
                <div class="summary-line">
                    <span>Phí Vận Chuyển:</span>
                    <span class="amount">{{ $shippingFee > 0 ? number_format($shippingFee, 0, ',', '.') . '₫' : 'Miễn phí' }}</span>
                </div>
                
                <div class="summary-line total-line">
                    <strong>Tổng Cộng:</strong>
                    <strong class="total-amount-display" style="color: #d9534f; font-size: 1.2em;">{{ number_format($finalTotal, 0, ',', '.') }}₫</strong>
                </div>

                <button type="submit" class="btn btn-place-order" style="background-color: #d9534f; color: white; width: 100%; padding: 15px; border: none; font-weight: bold; font-size: 18px; margin-top: 20px; cursor: pointer; border-radius: 4px;">
                    ĐẶT HÀNG
                </button>
            </div>
        </form>
    </div>
</div>
@endsection