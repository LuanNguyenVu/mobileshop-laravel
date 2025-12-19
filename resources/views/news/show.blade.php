@extends('layouts.app')

@section('title', $post->title)

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/news.css') }}">
@endpush

@section('content')
<div class="news-page">
    <div class="container">
        <div class="news-layout">
            
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
                        <img src="{{ asset('uploads/' . $post->thumbnail_path) }}" class="featured-image" alt="{{ $post->title }}">
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

            <aside class="news-sidebar">
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
                                <img src="{{ asset('uploads/' . $product->product_image) }}" class="sidebar-prod-img" alt="{{ $product->product_name }}">
                            </a>
                            <div class="sidebar-prod-info">
                                <h4><a href="{{ route('products.show', $product->id) }}">{{ $product->product_name }}</a></h4>
                                <div class="sidebar-price">{{ number_format($finalPrice, 0, ',', '.') }}₫</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>

        </div>
    </div>
</div>
@endsection