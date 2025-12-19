@extends('layouts.app')

@section('title', 'Giỏ Hàng')

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/cart.css') }}">
@endpush

@section('content')
<div class="container cart-page">
    <div class="cart-content">
        <h2>Giỏ Hàng (<span id="cart-count">{{ count($cartItems) }}</span> Sản Phẩm)</h2>
        
        @if ($cartItems->isEmpty())
            <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 8px;">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" alt="Empty Cart" style="width: 150px; opacity: 0.8;">
                <p style="margin-top: 15px; color: #666;">Giỏ hàng của bạn đang trống.</p>
                <a href="{{ route('products.index') }}" class="btn-buy" style="display:inline-block; width:auto; margin-top:15px; padding: 10px 25px;">
                    Tiếp Tục Mua Hàng
                </a>
            </div>
        @else
            <div class="cart-layout-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                
                {{-- DANH SÁCH SẢN PHẨM --}}
                <div class="cart-list">
                    @php $totalAmount = 0; @endphp
                    
                    @foreach ($cartItems as $item)
                        @php
                            $variant = $item->variant;
                            $product = $variant->product;
                            $price = ($variant->promotional_price > 0) ? $variant->promotional_price : $variant->selling_price;
                            $subtotal = $item->quantity * $price;
                            $totalAmount += $subtotal;
                        @endphp

                        <div class="cart-item" style="display: flex; align-items: center; padding: 15px; background: #fff; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            
                            {{-- Ảnh & Tên --}}
                            <div class="item-info" style="flex: 2; display: flex; align-items: center; gap: 15px;">
                                <a href="{{ route('products.show', $product->id) }}">
                                    <img src="{{ asset($product->product_image) }}" alt="{{ $product->product_name }}" style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #eee; border-radius: 6px;">
                                </a>
                                <div>
                                    <h4 style="margin: 0 0 5px 0; font-size: 15px;">
                                        <a href="{{ route('products.show', $product->id) }}" style="text-decoration:none; color:#333;">
                                            {{ $product->product_name }}
                                        </a>
                                    </h4>
                                    <span style="font-size: 13px; background: #f5f5f5; padding: 3px 8px; border-radius: 4px; color: #666;">
                                        Màu: {{ $variant->color }}
                                    </span>
                                    <p class="current-price" style="margin-top: 5px; font-weight: bold; color: #d70018;">
                                        {{ number_format($price, 0, ',', '.') }}₫
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Số lượng --}}
                            <div class="item-quantity" style="flex: 1; text-align: center;">
                                <input type="number" 
                                       value="{{ $item->quantity }}" 
                                       min="1" 
                                       max="{{ $variant->quantity }}" 
                                       data-id="{{ $item->id }}"
                                       class="quantity-update-input"
                                       style="width: 60px; padding: 5px; text-align: center; border: 1px solid #ddd; border-radius: 4px;"
                                       onchange="updateQuantity(this)">
                            </div>
                            
                            {{-- Thành tiền --}}
                            <div class="item-subtotal" style="flex: 1; text-align: center; font-weight: bold; color: #333;">
                                {{ number_format($subtotal, 0, ',', '.') }}₫
                            </div>
                            
                            {{-- NÚT XÓA (Đã được làm đẹp) --}}
                            <div class="item-actions" style="width: 50px;">
                                <a href="{{ route('cart.remove', $item->id) }}" 
                                   class="btn-remove-item"
                                   class="fa-times" 
                                   title="Xóa sản phẩm này"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TỔNG TIỀN --}}
                <div class="cart-summary" style="background: #fff; padding: 20px; border-radius: 8px; height: fit-content; position: sticky; top: 90px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-bottom: 20px; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Thanh toán</h3>
                    
                    <div class="summary-line" style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 16px;">
                        <span>Tạm tính:</span>
                        <strong>{{ number_format($totalAmount, 0, ',', '.') }}₫</strong>
                    </div>
                    
                    <div class="summary-line total-line" style="display: flex; justify-content: space-between; margin-bottom: 25px; font-size: 18px; color: #d70018;">
                        <strong>Tổng cộng:</strong>
                        <strong>{{ number_format($totalAmount, 0, ',', '.') }}₫</strong>
                    </div>
                    
                    <a href="{{ route('checkout') }}" class="btn-buy" style="display: block; width: 100%; text-align: center; padding: 12px; background: #d70018; color: #fff; font-weight: bold; border-radius: 4px; text-decoration: none; text-transform: uppercase;">
                        Thanh Toán Ngay
                    </a>
                    
                    <a href="{{ route('products.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #666; font-size: 13px; text-decoration: underline;">
                        ← Mua thêm sản phẩm khác
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateQuantity(input) {
        const itemId = input.dataset.id;
        const newQty = input.value;

        if(newQty < 1) {
            alert('Số lượng tối thiểu là 1');
            input.value = 1;
            return;
        }

        fetch('{{ route("cart.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id: itemId,
                quantity: newQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                location.reload(); 
            } else {
                alert(data.message);
                input.value = input.defaultValue; 
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối server');
        });
    }
</script>
@endsection