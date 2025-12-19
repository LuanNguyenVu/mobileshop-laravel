@extends('layouts.app')

@section('title', 'Tin Tức Công Nghệ')

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/news.css') }}">
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
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                        @foreach($posts as $post)
                        <div class="news-card" data-aos="fade-up">
                            <div class="card-img-wrapper">
                                <a href="{{ route('news.show', $post->slug) }}">
                                <img src="{{ asset($post->thumbnail_path ?? 'assets/client/images/no-image.jpg') }}" 
                                    alt="{{ $post->title }}">
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="card-meta">
                                    <span><i class="far fa-calendar-alt"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                                    <span><i class="far fa-user"></i> Admin</span>
                                </div>
                                <h3 class="card-title">
                                    <a href="{{ route('news.show', $post->slug) }}">{{ Str::limit($post->title, 55) }}</a>
                                </h3>
                                <div class="card-excerpt">
                                    {{ Str::limit(strip_tags($post->content), 100) }}
                                </div>
                                <a href="{{ route('news.show', $post->slug) }}" class="btn-read-more">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="pagination-wrapper d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

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
                                <small style="color: #ffc107;"><i class="fas fa-star"></i> 5.0</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Banner quảng cáo nhỏ (nếu có) --}}
                <div class="sidebar-widget" style="padding: 0; overflow: hidden;" data-aos="fade-left" data-aos-delay="100">
                    <img src="https://via.placeholder.com/400x300?text=ADS+BANNER" style="width: 100%; display: block;" alt="Ads">
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