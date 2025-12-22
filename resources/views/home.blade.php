@extends('layouts.app')

@section('title', 'Trang Chủ - MobileShop')

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/home.css') }}">
@endpush

@section('content')
<div class="container">
    
    {{-- === PHẦN MỚI: 2 BANNER QUẢNG CÁO TỰ ĐỘNG (Lấy từ DB) === --}}
    @if($headerAds && $headerAds->count() > 0)
    <div class="hero-promo-section">
        @foreach($headerAds as $ad)
            <div class="promo-card">
                {{-- Link ảnh --}}
                <a href="#" title="{{ $ad->title }}">
                    <img src="{{ asset($ad->image_path) }}" alt="{{ $ad->title }}">
                    
                    {{-- Nội dung chữ (Nếu muốn hiện title lên ảnh) --}}
                    <div class="promo-content">
                        <h3>{{ Str::limit($ad->title, 25) }}</h3>
                        {{-- Logic hiển thị nút nhỏ --}}
                        <span style="background: #d70018; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                            HOT DEAL
                        </span>
                    </div>
                </a>
            </div>
        @endforeach

        {{-- Nếu chỉ có 1 banner, hiển thị thêm 1 cái mặc định để layout không bị vỡ --}}
        @if($headerAds->count() === 1)
            <div class="promo-card">
                <a href="#">
                    <img src="https://images.unsplash.com/photo-1556656793-02715d8dd660?auto=format&fit=crop&w=1000&q=80" alt="Default Banner">
                    <div class="promo-content">
                        <h3>KHUYẾN MÃI ĐẶC BIỆT</h3>
                        <span style="background: #d70018; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">XEM NGAY</span>
                    </div>
                </a>
            </div>
        @endif
    </div>
    @endif
    {{-- === KẾT THÚC PHẦN MỚI === --}}

    {{-- PHẦN 1: BANNER SLIDER VÀ TIN TỨC --}}
    <div class="top-section">
        
        {{-- Main Slider (Sử dụng biến $sliderAds thay vì $advertisements cũ) --}}
        <div class="main-banner-area">
            <div class="slider-container">
                @forelse($sliderAds as $index => $ad)
                    <div class="slide-item" data-index="{{ $index }}">
                        <a href="#" title="{{ $ad->title }}">
                            <img src="{{ asset($ad->image_path) }}" 
                                 alt="{{ $ad->title }}" 
                                 class="slider-img">
                        </a>
                    </div>
                @empty
                    <div class="slide-item">
                        <img src="https://via.placeholder.com/800x350?text=Slider+Mac+Dinh" class="slider-img">
                    </div>
                @endforelse
            </div>
            
            <button class="slider-prev">&#10094;</button>
            <button class="slider-next">&#10095;</button>
            
            <div class="slider-navigation">
                @foreach($sliderAds as $index => $ad)
                    <div class="nav-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                        {{ $ad->title }}
                    </div>
                @endforeach
            </div>
        </div>
        
        {{-- Sidebar Tin tức (Giữ nguyên) --}}
        <div class="news-sidebar">
            <h3 class="sidebar-title">TIN CÔNG NGHỆ</h3>
            <ul class="post-list">
                @forelse($posts as $post)
                    <li class="post-item-sidebar">
                        <a href="{{ route('news.show', $post->slug) }}">
                            <img src="{{ asset($post->thumbnail_path ?? 'client/assets/client/images/no-image.jpg') }}" alt="{{ $post->title }}">
                            <div class="post-details">
                                <p class="post-titles">{{ Str::limit($post->title, 50) }}</p>
                                <small class="post-date">{{ $post->created_at->format('d/m/Y') }}</small>
                            </div>
                        </a>
                    </li>
                @empty
                    <li style="padding:15px; color: #666; font-size: 13px;">Chưa có bài viết nào.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ... CÁC PHẦN SẢN PHẨM BÊN DƯỚI GIỮ NGUYÊN ... --}}
    
    {{-- PHẦN 1.5: SLIDER SẢN PHẨM MỚI NHẤT --}}
    <div class="product-slider-section" style="margin-top: 40px; background: #fff; padding: 20px 20px 30px 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        
        {{-- 1. Tiêu đề (Chỉ để tên, bỏ nút bấm đi) --}}
        <div class="section-header" style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 class="section-title" style="margin: 0; font-size: 20px; color: #fdfcfcff; text-transform: uppercase; font-weight: 800;">
                <i class="fas fa-bolt" style="margin-right: 5px;"></i> SẢN PHẨM MỚI VỀ
            </h2>
        </div>

        {{-- 2. Khu vực Slider (Bao gồm cả nút bấm và dải sản phẩm) --}}
        <div class="slider-body-container" style="position: relative;">
            
            {{-- Nút Trái (Prev) --}}
            <button id="btn-prev-new" class="slider-nav-btn" style="left: -15px;">
                <i class="fas fa-chevron-left"></i>
            </button>

            {{-- Khung chứa sản phẩm (Ẩn phần thừa) --}}
            <div class="product-carousel-wrapper" style="overflow: hidden; padding: 5px 0;"> 
                <div class="product-carousel" id="newProductTrack" style="display: flex; gap: 15px; transition: transform 0.5s ease;">
                    @foreach($newProducts as $product)
                        @php
                            $minVariant = $product->variants->sortBy('selling_price')->first();
                            $selling_price = $minVariant ? $minVariant->selling_price : 0;
                            $promotional_price = $minVariant ? $minVariant->promotional_price : 0;
                            $is_promo = ($promotional_price > 0 && $promotional_price < $selling_price);
                            $discount_percent = ($is_promo && $selling_price > 0) ? round((($selling_price - $promotional_price) / $selling_price) * 100) : 0;
                        @endphp

                        {{-- Card sản phẩm --}}
                        <div class="product-card-slide" style="min-width: 200px; max-width: 200px; border: 1px solid #f0f0f0; border-radius: 8px; padding: 10px; position: relative; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            @if ($is_promo && $discount_percent > 0)
                                <span style="position: absolute; top: 5px; left: -5px; background: #d70018; color: #fff; font-size: 10px; padding: 2px 5px; border-radius: 4px; font-weight: bold; z-index: 2;">-{{ $discount_percent }}%</span>
                            @endif
                            
                            <a href="{{ route('products.show', $product->id) }}" style="display: block; text-align: center; height: 160px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ asset($product->product_image) }}" alt="{{ $product->product_name }}" style="max-width: 100%; max-height: 150px; object-fit: contain;">
                            </a>
                            
                            <div class="info" style="margin-top: 10px;">
                                <a href="{{ route('products.show', $product->id) }}" style="text-decoration: none; color: #333;">
                                    <h4 style="font-size: 13px; font-weight: 600; margin: 0 0 5px 0; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->product_name }}</h4>
                                </a>
                                
                                <div class="price" style="font-weight: 700; color: #d70018;">
                                    {{ number_format($is_promo ? $promotional_price : $selling_price, 0, ',', '.') }}₫
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Nút Phải (Next) --}}
            <button id="btn-next-new" class="slider-nav-btn" style="right: -15px;">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    {{-- PHẦN 2: SẢN PHẨM ƯA THÍCH (GRID) --}}
    <div class="product-list-section">
        <div class="section-header">
            <h2 class="section-title">SẢN PHẨM ƯA THÍCH HÔM NAY</h2>
        </div>
        
        <div class="product-grid">
            @forelse($featuredProducts as $product)
                @php
                    // Lấy biến thể có giá thấp nhất
                    $minVariant = $product->variants->sortBy('selling_price')->first();
                    $selling_price = $minVariant ? $minVariant->selling_price : 0;
                    $promotional_price = $minVariant ? $minVariant->promotional_price : 0;
                    
                    // Logic tính khuyến mãi
                    $is_promo = ($promotional_price > 0 && $promotional_price < $selling_price);
                    $discount_percent = 0;
                    if ($is_promo && $selling_price > 0) {
                        $discount_percent = round((($selling_price - $promotional_price) / $selling_price) * 100);
                    }
                @endphp

                <div class="product-card">
                    {{-- Badge giảm giá --}}
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
                        <span>RAM: {{ $product->ram ?? 'N/A' }}</span> | 
                        <span>ROM: {{ $product->rom ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($product->rating >= $i)
                                <i class="fas fa-star checked" style="color:#ffc107"></i>
                            @elseif ($product->rating > $i - 1)
                                <i class="fas fa-star-half-alt checked" style="color:#ffc107"></i>
                            @else
                                <i class="far fa-star" style="color:#ddd"></i>
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
                                                        
                    {{-- Tooltip khi hover --}}
                    <div class="product-specs-tooltip">
                        <div class="tooltip-header">CẤU HÌNH NỔI BẬT</div>
                        <ul>
                            <li><i class="fas fa-microchip"></i> RAM: <span>{{ $product->ram }}</span></li>
                            <li><i class="fas fa-sd-card"></i> ROM: <span>{{ $product->rom }}</span></li>
                            <li><i class="fas fa-camera"></i> Camera: <span>{{ $product->camera }}</span></li>
                            <li><i class="fas fa-battery-full"></i> Pin: <span>{{ $product->battery }}</span></li>
                        </ul>
                        <a href="{{ route('products.show', $product->id) }}" class="btn-detail">Xem chi tiết</a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 30px;">
                    <p class="no-products-msg">Hiện chưa có sản phẩm nổi bật nào.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script cho Slider (Giữ nguyên) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let slideIndex = 0;
        const slides = document.querySelectorAll('.slide-item');
        const navItems = document.querySelectorAll('.nav-item');
        
        if (slides.length > 0) {
            slides[0].style.display = 'block';
            slides[0].classList.add('active');
            if(navItems[0]) navItems[0].classList.add('active');

            function showSlide(n) {
                slides.forEach(slide => {
                    slide.style.display = 'none';
                    slide.classList.remove('active');
                });
                navItems.forEach(item => item.classList.remove('active'));
                
                slideIndex = n;
                if (slideIndex >= slides.length) slideIndex = 0;
                if (slideIndex < 0) slideIndex = slides.length - 1;
                
                slides[slideIndex].style.display = 'block';
                setTimeout(() => slides[slideIndex].classList.add('active'), 10);
                
                if(navItems[slideIndex]) navItems[slideIndex].classList.add('active');
            }

            const nextBtn = document.querySelector('.slider-next');
            const prevBtn = document.querySelector('.slider-prev');

            if(nextBtn) nextBtn.addEventListener('click', () => showSlide(slideIndex + 1));
            if(prevBtn) prevBtn.addEventListener('click', () => showSlide(slideIndex - 1));
            
            navItems.forEach((item, index) => {
                item.addEventListener('click', () => showSlide(index));
            });

            let autoPlay = setInterval(() => showSlide(slideIndex + 1), 3000);

            const bannerArea = document.querySelector('.main-banner-area');
            if(bannerArea) {
                bannerArea.addEventListener('mouseenter', () => clearInterval(autoPlay));
                bannerArea.addEventListener('mouseleave', () => {
                    autoPlay = setInterval(() => showSlide(slideIndex + 1), 3000);
                });
            }
        }
        
        // --- JS CHO SLIDER SẢN PHẨM MỚI ---
        const track = document.getElementById('newProductTrack');
        const btnPrev = document.getElementById('btn-prev-new');
        const btnNext = document.getElementById('btn-next-new');
        
        const cardWidth = 215; 
        let currentPosition = 0;
        
        if(track && btnPrev && btnNext) {
            btnNext.addEventListener('click', () => {
                const containerWidth = track.parentElement.offsetWidth;
                const trackWidth = track.scrollWidth; 
                const maxTranslate = -(trackWidth - containerWidth);

                currentPosition -= cardWidth; 
                
                if (currentPosition < maxTranslate) {
                    currentPosition = 0; 
                }
                track.style.transform = `translateX(${currentPosition}px)`;
            });

            btnPrev.addEventListener('click', () => {
                currentPosition += cardWidth;
                
                if (currentPosition > 0) {
                    currentPosition = 0; 
                }
                track.style.transform = `translateX(${currentPosition}px)`;
            });
        }
    });
</script>
@endpush