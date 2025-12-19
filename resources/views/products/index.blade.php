@extends('layouts.app')

@section('title', 'Danh Sách Sản Phẩm')

{{-- Đẩy CSS riêng của trang này vào stack css --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/products.css') }}">
@endpush

@section('content')
<div class="container product-page-container">
    
    <div class="search-sort-area">
        <h3>TÌM KIẾM VÀ BỘ LỌC</h3>
        <form action="{{ route('products.index') }}" method="GET" class="filter-row">
            {{-- Giữ lại tham số brand nếu có --}}
            @if(request('brand'))
                <input type="hidden" name="brand" value="{{ request('brand') }}">
            @endif

            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tên sản phẩm...">
            
            <select name="os">
                <option value="">-- Hệ Điều Hành --</option>
                @foreach ($os_options as $os)
                    <option value="{{ $os }}" {{ request('os') == $os ? 'selected' : '' }}>
                        {{ $os }}
                    </option>
                @endforeach
            </select>
            
            <select name="type">
                <option value="">-- Loại Sản Phẩm --</option>
                @foreach ($product_types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            
            <select name="sort">
                <option value="default" {{ request('sort') == 'default' ? 'selected' : '' }}>Mới nhất</option>
                <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Đánh giá cao</option>
            </select>
            
            <button type="submit"><i class="fas fa-filter"></i> LỌC NGAY</button>
            
            @if(request()->hasAny(['keyword', 'os', 'type', 'brand']))
                <a href="{{ route('products.index') }}" style="color: #666; text-decoration: underline; font-size: 14px;">Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    <div class="brand-list">
        <a href="{{ route('products.index') }}" class="brand-tag {{ !request('brand') ? 'active' : '' }}">Tất cả</a>
        @foreach ($brands as $brand)
            <a href="{{ route('products.index', array_merge(request()->all(), ['brand' => $brand, 'page' => null])) }}" 
               class="brand-tag {{ request('brand') == $brand ? 'active' : '' }}">
                {{ $brand }}
            </a>
        @endforeach
    </div>
    
    <h2 class="current-brand-title">{{ $current_brand }}</h2>

    <div class="product-grid">
        @forelse($products as $product)
            @php
                // Logic lấy giá thấp nhất và tính khuyến mãi (giống trang chủ)
                $minVariant = $product->variants->sortBy('selling_price')->first();
                $selling_price = $minVariant ? $minVariant->selling_price : 0;
                $promotional_price = $minVariant ? $minVariant->promotional_price : 0;
                $is_promo = ($promotional_price > 0 && $promotional_price < $selling_price);
                $discount_percent = 0;
                if ($is_promo && $selling_price > 0) {
                    $discount_percent = round((($selling_price - $promotional_price) / $selling_price) * 100);
                }
            @endphp

            <div class="product-card">
                @if ($is_promo && $discount_percent > 0)
                    <span class="discount-badge">Giảm {{ $discount_percent }}%</span>
                @endif
                
                <div class="product-image-wrapper">
                    <a href="{{ route('products.show', $product->id) }}">
                        <img src="{{ asset($product->product_image) }}" alt="{{ $product->product_name }}">
                    </a>
                </div>
                
                <a href="{{ route('products.show', $product->id) }}" style="text-decoration: none;">
                    <h4 class="product-name-title">{{ $product->product_name }}</h4>
                </a>

                <div class="specs-quick">
                    <span>RAM: {{ $product->ram }}</span> | <span>ROM: {{ $product->rom }}</span>
                </div>
                
                <div class="rating-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($product->rating >= $i)
                            <i class="fas fa-star checked"></i>
                        @elseif ($product->rating > $i - 1)
                            <i class="fas fa-star-half-alt checked"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>

                <p class="price-section">
                    @if ($is_promo)
                        <span class="current-price">{{ number_format($promotional_price, 0, ',', '.') }}₫</span>
                        <span class="old-price">{{ number_format($selling_price, 0, ',', '.') }}₫</span>
                    @else
                        <span class="current-price">{{ number_format($selling_price, 0, ',', '.') }}₫</span>
                    @endif
                </p>
            </div>
        @empty
            <div class="no-products-msg" style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #fff; border-radius: 8px;">
                <i class="far fa-frown" style="font-size: 40px; color: #ccc; margin-bottom: 15px;"></i>
                <p>Không tìm thấy sản phẩm nào phù hợp với tiêu chí lọc.</p>
                <a href="{{ route('products.index') }}" class="btn-detail" style="display: inline-block; margin-top: 10px;">Xem tất cả sản phẩm</a>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $products->links() }} 
        {{-- Laravel tự động sinh HTML phân trang Bootstrap/Tailwind --}}
    </div>

</div>
@endsection