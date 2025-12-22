@extends('layouts.app')

@section('title', 'Tin Tức Công Nghệ')

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/news.css') }}">
    <style>
        /* === CSS RIÊNG CHO LAYOUT TIN TỨC MỚI === */

        /* 1. Style cho Bài viết nổi bật (Banner to) */
        .featured-news-card {
            position: relative;
            width: 100%;
            height: 450px; /* Chiều cao cố định cho banner */
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .featured-news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .featured-news-card img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Đảm bảo ảnh không bị méo */
            transition: transform 0.5s ease;
        }

        .featured-news-card:hover img {
            transform: scale(1.05); /* Zoom nhẹ ảnh khi hover */
        }

        /* Lớp phủ đen mờ để chữ dễ đọc hơn */
        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            /* Gradient từ đen sang trong suốt */
            background: linear-gradient(to top, rgba(0,0,0,0.9) 10%, rgba(0,0,0,0.6) 50%, transparent 100%);
            padding: 30px;
            z-index: 2;
            color: #fff;
        }

        .featured-title {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .featured-title a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .featured-title a:hover {
            color: #d70018;
        }

        .featured-meta {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
            color: #ddd;
        }

        .featured-excerpt {
            font-size: 15px;
            color: #eee;
            max-width: 80%;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Giới hạn 2 dòng */
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Link bao phủ toàn bộ thẻ để click vào đâu cũng được */
        .full-card-link {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 3;
        }

        /* 2. Style cho Danh sách bài viết bên dưới (Ngang) */
        .news-list-item {
            display: flex; /* Xếp ngang */
            gap: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #eee;
            transition: 0.3s;
        }

        .news-list-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-color: #d70018;
        }

        .news-list-thumb {
            flex: 0 0 280px; /* Chiều rộng cố định của ảnh */
            height: 180px;
            border-radius: 6px;
            overflow: hidden;
        }

        .news-list-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.3s;
        }
        
        .news-list-item:hover .news-list-thumb img {
            transform: scale(1.05);
        }

        .news-list-content {
            flex: 1; /* Chiếm phần còn lại */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .news-list-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .news-list-title a {
            color: #333;
            text-decoration: none;
        }
        
        .news-list-title a:hover {
            color: #d70018;
        }

        /* Responsive cho Mobile */
        @media (max-width: 768px) {
            .featured-news-card {
                height: 300px;
            }
            .featured-title {
                font-size: 18px;
            }
            .news-list-item {
                flex-direction: column; /* Mobile thì quay về dọc */
            }
            .news-list-thumb {
                flex: none;
                width: 100%;
                height: 200px;
            }
        }
    </style>
@endpush

@section('content')
<div class="news-page">
    <div class="container">
        <div class="news-header" style="margin-bottom: 40px; text-align: center;" data-aos="fade-down">
            <h1 style="font-size: 2.5rem; font-weight: 800; color: #333;">TIN TỨC CÔNG NGHỆ</h1>
            <p style="color: #666;">Cập nhật những xu hướng mới nhất thế giới di động</p>
            <div style="width: 60px; height: 4px; background: #cc0000; margin: 20px auto;"></div>
        </div>

        <div class="news-layout">
            
            <div class="news-main-column">
                @if($posts->isEmpty())
                    <div style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                        <p>Hiện chưa có bài viết nào được xuất bản.</p>
                    </div>
                @else
                    {{-- 
                        LOGIC XỬ LÝ:
                        - Lấy bài đầu tiên làm Featured (Banner to)
                        - Các bài còn lại hiển thị dạng list dọc
                    --}}
                    @php
                        $featuredPost = $posts->first(); // Bài mới nhất
                        $listPosts = $posts->slice(1, 5); // Lấy 5 bài tiếp theo (bỏ bài đầu)
                    @endphp

                    {{-- 1. BÀI VIẾT NỔI BẬT (BANNER TO) --}}
                    @if($featuredPost)
                        <div class="featured-news-card" data-aos="zoom-in">
                            {{-- Link tàng hình bao phủ toàn bộ --}}
                            <a href="{{ route('news.show', $featuredPost->slug) }}" class="full-card-link"></a>
                            
                            <img src="{{ asset($featuredPost->thumbnail_path ?? 'assets/client/images/no-image.jpg') }}" 
                                 alt="{{ $featuredPost->title }}">
                            
                            <div class="featured-overlay">
                                <div class="featured-meta">
                                    <i class="far fa-calendar-alt"></i> {{ $featuredPost->created_at->format('d/m/Y') }} 
                                    &nbsp;|&nbsp; 
                                    <i class="far fa-user"></i> Admin
                                </div>
                                <h2 class="featured-title">
                                    {{ Str::limit($featuredPost->title, 80) }}
                                </h2>
                                <p class="featured-excerpt">
                                    {{ Str::limit(strip_tags($featuredPost->content), 150) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- 2. DANH SÁCH BÀI VIẾT TIẾP THEO (DỌC - ẢNH TRÁI CHỮ PHẢI) --}}
                    <div class="news-list-container">
                        @foreach($listPosts as $post)
                            <div class="news-list-item" data-aos="fade-up">
                                <div class="news-list-thumb">
                                    <a href="{{ route('news.show', $post->slug) }}">
                                        <img src="{{ asset($post->thumbnail_path ?? 'assets/client/images/no-image.jpg') }}" 
                                             alt="{{ $post->title }}">
                                    </a>
                                </div>
                                
                                <div class="news-list-content">
                                    <div class="card-meta" style="color: #888; font-size: 13px; margin-bottom: 5px;">
                                        <span><i class="far fa-calendar-alt"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <h3 class="news-list-title">
                                        <a href="{{ route('news.show', $post->slug) }}">{{ Str::limit($post->title, 70) }}</a>
                                    </h3>
                                    <div class="card-excerpt" style="color: #666; font-size: 14px; line-height: 1.5;">
                                        {{ Str::limit(strip_tags($post->content), 120) }}
                                    </div>
                                    <div style="margin-top: 10px;">
                                        <a href="{{ route('news.show', $post->slug) }}" style="color: #d70018; font-weight: 600; font-size: 13px; text-decoration: none;">
                                            Xem chi tiết <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Phân trang (Nếu có nhiều hơn 6 bài thì hiện phân trang) --}}
                    <div class="pagination-wrapper d-flex justify-content-center" style="margin-top: 30px;">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            {{-- SIDEBAR GIỮ NGUYÊN --}}
            <aside class="news-sidebar">
                <div class="sidebar-widget" data-aos="fade-left">
                    <h3 class="widget-title">Sản Phẩm Mới Nhất</h3>
                    
                    @foreach($latestProducts as $product)
                        @php
                            $minVariant = $product->variants->sortBy('selling_price')->first();
                            $price = $minVariant ? $minVariant->selling_price : 0;
                            $promo = $minVariant ? $minVariant->promotional_price : 0;
                            $finalPrice = ($promo > 0 && $promo < $price) ? $promo : $price;
                        @endphp
                        <div class="sidebar-product-item">
                            <a href="{{ route('products.show', $product->id) }}">
                                <img src="{{ asset($product->product_image) }}" class="sidebar-prod-img" alt="{{ $product->product_name }}">
                            </a>
                            <div class="sidebar-prod-info">
                                <h4>
                                    <a href="{{ route('products.show', $product->id) }}">{{ $product->product_name }}</a>
                                </h4>
                                <div class="sidebar-price">{{ number_format($finalPrice, 0, ',', '.') }}₫</div>
                                <small style="color: #ffc107;">
                                    <i class="fas fa-star"></i> {{ number_format($product->rating, 1) }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800 });
</script>
@endsection