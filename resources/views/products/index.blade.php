@extends('layouts.app')

@section('title', 'Danh Sách Sản Phẩm')

@push('css')
    {{-- Link CSS gốc của trang sản phẩm --}}
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/products.css') }}">

    {{-- CSS HIỆU ỨNG ĐỘNG CHO BANNER (COPY TỪ TRANG CHỦ) --}}
    <style>
        /* === CONTAINER BANNER === */
        .hero-promo-section {
            display: flex;
            gap: 15px;
            margin-bottom: 30px; /* Cách phần bộ lọc bên dưới */
            height: 220px;
            overflow: hidden;
            perspective: 1500px; /* Chiều sâu 3D */
        }

        .promo-card {
            flex: 1;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background: #000;
            
            /* Animation Lật (Flip) khi vào trang */
            opacity: 0;
            transform-origin: center top;
            animation: flipIn 1s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .promo-card:nth-child(2) {
            animation-delay: 0.2s; /* Card 2 lật chậm hơn chút */
        }

        @keyframes flipIn {
            0% { opacity: 0; transform: rotateX(-90deg); }
            100% { opacity: 1; transform: rotateX(0deg); }
        }

        /* Hiệu ứng Zoom ảnh (Ken Burns) */
        .promo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            animation: kenBurns 15s ease-in-out infinite alternate;
            transform-origin: center center;
            opacity: 0.9;
        }

        @keyframes kenBurns {
            0% { transform: scale(1); }
            100% { transform: scale(1.15); }
        }

        /* Hiệu ứng vệt sáng lướt qua (Shine) */
        .promo-card::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            z-index: 2;
            animation: shine 4s infinite 1s;
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 200%; }
            100% { left: 200%; }
        }

        /* Nội dung chữ trên banner */
        .promo-content {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 3;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.8);
            pointer-events: none;
            opacity: 0;
            animation: fadeInText 0.5s ease-out forwards;
            animation-delay: 0.8s;
        }

        @keyframes fadeInText { to { opacity: 1; } }

        .promo-content h3 {
            font-size: 20px;
            font-weight: 800;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-promo-section { flex-direction: column; height: auto; }
            .promo-card { height: 160px; }
        }
    </style>
@endpush

@section('content')
<div class="container product-page-container">

    {{-- === PHẦN 1: 2 BANNER QUẢNG CÁO (MỚI THÊM) === --}}
    @if(isset($headerAds) && $headerAds->count() > 0)
        <div class="hero-promo-section">
            @foreach($headerAds as $ad)
                <div class="promo-card">
                    <a href="#" title="{{ $ad->title }}">
                        <img src="{{ asset($ad->image_path) }}" alt="{{ $ad->title }}">
                        <div class="promo-content">
                            <h3>{{ Str::limit($ad->title, 30) }}</h3>
                            <span style="background: #d70018; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                XEM NGAY
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach

            {{-- Fallback: Nếu chỉ có 1 banner thì hiển thị thêm 1 cái mặc định để đẹp giao diện --}}
            @if($headerAds->count() === 1)
                <div class="promo-card">
                    <a href="#">
                        <img src="https://images.unsplash.com/photo-1592434134753-a70baf7979d5?auto=format&fit=crop&w=1000&q=80" alt="Tech Banner">
                        <div class="promo-content">
                            <h3>CÔNG NGHỆ MỚI</h3>
                            <span style="background: #d70018; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">KHÁM PHÁ</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    @endif
    {{-- === KẾT THÚC PHẦN BANNER === --}}
    
    {{-- PHẦN 2: BỘ LỌC VÀ TÌM KIẾM (CŨ) --}}
    <div class="search-sort-area">
        <h3>TÌM KIẾM VÀ BỘ LỌC</h3>
        <form action="{{ route('products.index') }}" method="GET" class="filter-row">
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
    </div>

</div>
@endsection