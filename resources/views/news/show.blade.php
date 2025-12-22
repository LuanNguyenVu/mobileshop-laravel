@extends('layouts.app')

@section('title', $post->title)

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/news.css') }}">
    
    <style>
        /* CSS CHO TIN TỨC TRONG SIDEBAR (Giống sản phẩm sidebar) */
        .sidebar-news-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .sidebar-news-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .sidebar-news-img-wrapper {
            flex: 0 0 80px; /* Chiều rộng cố định ảnh nhỏ */
            height: 60px;
            border-radius: 4px;
            overflow: hidden;
        }

        .sidebar-news-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .sidebar-news-item:hover .sidebar-news-img-wrapper img {
            transform: scale(1.1);
        }

        .sidebar-news-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sidebar-news-info h4 {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 5px 0;
            line-height: 1.3;
        }

        .sidebar-news-info h4 a {
            color: #333;
            text-decoration: none;
            transition: color 0.2s;
        }

        .sidebar-news-info h4 a:hover {
            color: #d70018;
        }

        .sidebar-news-date {
            font-size: 11px;
            color: #888;
        }
    </style>
@endpush

@section('content')
<div class="news-page">
    <div class="container">
        <div class="news-layout">
            
            {{-- CỘT TRÁI: NỘI DUNG BÀI VIẾT --}}
            <div class="news-main-column">
                <div class="post-detail-container">
                    
                    <div class="breadcrumb-nav">
                        <a href="{{ route('home') }}">Trang chủ</a> / 
                        <a href="{{ route('news.index') }}">Tin tức</a> / 
                        <span>Chi tiết</span>
                    </div>

                    <h1 class="single-post-title">{{ $post->title }}</h1>
                    
                    <div class="single-post-meta">
                        <span><i class="far fa-calendar-alt"></i> {{ $post->created_at->format('d/m/Y H:i') }}</span>
                        <span><i class="far fa-user"></i> Admin</span>
                        <span><i class="far fa-eye"></i> 1,234 Lượt xem</span>
                    </div>

                    @if($post->thumbnail_path)
                        <img src="{{ asset($post->thumbnail_path) }}" class="featured-image" alt="{{ $post->title }}">
                    @endif

                    <div class="post-content">
                        {!! $post->content !!}
                    </div>

                    <div class="share-buttons">
                        <strong>Chia sẻ:</strong>
                        <a href="#" class="share-btn share-fb"><i class="fab fa-facebook-f"></i> Facebook</a>
                        <a href="#" class="share-btn share-tw"><i class="fab fa-twitter"></i> Twitter</a>
                    </div>
                    
                </div>
            </div>

            {{-- CỘT PHẢI: SIDEBAR (Sản phẩm + Bài viết khác) --}}
            <aside class="news-sidebar">
                
                {{-- WIDGET 1: SẢN PHẨM MỚI NHẤT --}}
                <div class="sidebar-widget">
                    <h3 class="widget-title">Sản Phẩm Mới Nhất</h3>
                    @foreach($latestProducts as $product)
                        @php
                            $minVariant = $product->variants->sortBy('selling_price')->first();
                            $price = $minVariant ? $minVariant->selling_price : 0;
                            $finalPrice = $minVariant && $minVariant->promotional_price > 0 && $minVariant->promotional_price < $price ? $minVariant->promotional_price : $price;
                        @endphp
                        <div class="sidebar-product-item">
                            <a href="{{ route('products.show', $product->id) }}">
                                <img src="{{ asset($product->product_image) }}" class="sidebar-prod-img" alt="{{ $product->product_name }}">
                            </a>
                            <div class="sidebar-prod-info">
                                <h4><a href="{{ route('products.show', $product->id) }}">{{ $product->product_name }}</a></h4>
                                <div class="sidebar-price">{{ number_format($finalPrice, 0, ',', '.') }}₫</div>
                                <small style="color: #ffc107;">
                                    <i class="fas fa-star"></i> {{ number_format($product->rating, 1) }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- WIDGET 2: BÀI VIẾT KHÁC (ĐÃ CHUYỂN VÀO ĐÂY) --}}
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                <div class="sidebar-widget" style="margin-top: 30px;">
                    <h3 class="widget-title">Bài Viết Khác</h3>
                    
                    @foreach($relatedPosts as $related)
                        <div class="sidebar-news-item">
                            {{-- Ảnh nhỏ bên trái --}}
                            <div class="sidebar-news-img-wrapper">
                                <a href="{{ route('news.show', $related->slug) }}">
                                    <img src="{{ asset($related->thumbnail_path ?? 'assets/client/images/no-image.jpg') }}" 
                                         alt="{{ $related->title }}">
                                </a>
                            </div>
                            {{-- Thông tin bên phải --}}
                            <div class="sidebar-news-info">
                                <h4>
                                    <a href="{{ route('news.show', $related->slug) }}">
                                        {{ Str::limit($related->title, 50) }}
                                    </a>
                                </h4>
                                <span class="sidebar-news-date">
                                    <i class="far fa-calendar-alt"></i> {{ $related->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
                {{-- KẾT THÚC WIDGET BÀI VIẾT KHÁC --}}

            </aside>

        </div>
    </div>
</div>
@endsection