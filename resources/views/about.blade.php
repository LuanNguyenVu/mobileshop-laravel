@extends('layouts.app')

@section('title', 'Về Chúng Tôi - Mobile Shop')

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/about.css') }}">
@endpush

@section('content')
<div class="about-page">

    {{-- 1. HERO SECTION --}}
    <section class="about-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content" data-aos="zoom-in" data-aos-duration="1000">
            <h1 class="hero-title">MobileShop</h1>
            <p class="hero-subtitle">Hơn cả một cửa hàng điện thoại. Chúng tôi là cầu nối đưa công nghệ tương lai đến tận tay bạn.</p>
        </div>
    </section>

    {{-- 2. STATS SECTION (Floating) --}}
    <div class="stats-container">
        <div class="stats-grid" data-aos="fade-up" data-aos-offset="-50">
            <div class="stat-item">
                <h3>10+</h3>
                <p>Năm Kinh Nghiệm</p>
            </div>
            <div class="stat-item">
                <h3>50K+</h3>
                <p>Khách Hàng Hài Lòng</p>
            </div>
            <div class="stat-item">
                <h3>4.9/5</h3>
                <p>Đánh Giá Google</p>
            </div>
        </div>
    </div>

    {{-- 3. STORY SECTION --}}
    <section class="section-spacing story-section">
        <div class="story-grid">
            <div class="story-content" data-aos="fade-right">
                <h2>Câu Chuyện Khởi Nguồn</h2>
                <p>Thành lập từ năm 2010, MobileShop bắt đầu từ một cửa hàng sửa chữa nhỏ trong con ngõ Sài Gòn. Với niềm đam mê mãnh liệt với công nghệ, chúng tôi không chỉ bán điện thoại, chúng tôi bán trải nghiệm.</p>
                <p>Chúng tôi tin rằng: <strong>"Công nghệ không nên là thứ xa xỉ, nó phải là công cụ giúp cuộc sống tốt đẹp hơn."</strong> Đó là lý do MobileShop luôn cam kết giá tốt nhất với chất lượng chuẩn hãng.</p>
            </div>
            <div class="story-img" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Our Story">
            </div>
        </div>
    </section>

    {{-- 4. CORE VALUES --}}
    <section class="section-spacing values-section">
        <div class="container">
            <h2 class="story-content" style="display:inline-block; font-size:2.5rem; color:#333; font-weight:700;" data-aos="fade-up">Giá Trị Cốt Lõi</h2>
            <div class="values-grid">
                <div class="value-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-icon-box"><i class="fas fa-gem"></i></div>
                    <h3>Chất Lượng Thật</h3>
                    <p>Cam kết 100% sản phẩm chính hãng. Phát hiện hàng giả đền bù gấp 10 lần giá trị.</p>
                </div>
                <div class="value-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-icon-box"><i class="fas fa-shipping-fast"></i></div>
                    <h3>Tốc Độ Thần Tốc</h3>
                    <p>Giao hàng trong 2h nội thành. Hỗ trợ kỹ thuật 24/7 không để bạn chờ đợi.</p>
                </div>
                <div class="value-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-icon-box"><i class="fas fa-heart"></i></div>
                    <h3>Tận Tâm Phục Vụ</h3>
                    <p>Khách hàng là người thân. Chúng tôi lắng nghe và giải quyết mọi vấn đề của bạn.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. TEAM SECTION --}}
    <section class="section-spacing team-section">
        <div class="container">
            <h2 style="font-size:2.5rem; color:#333; font-weight:700; margin-bottom:10px;" data-aos="fade-up">Đội Ngũ Lãnh Đạo</h2>
            <p style="color:#666;" data-aos="fade-up">Những người thuyền trưởng chèo lái con tàu MobileShop</p>
            
            <div class="team-grid">
                <div class="team-member" data-aos="flip-left">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80" class="member-img" alt="CEO">
                    <div class="member-info">
                        <h4>Nguyễn Vũ Luân</h4>
                        <p>Founder & CEO</p>
                    </div>
                </div>

                <div class="team-member" data-aos="flip-left" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80" class="member-img" alt="CTO">
                    <div class="member-info">
                        <h4>Trần Thị Mai</h4>
                        <p>Giám đốc Sản phẩm</p>
                    </div>
                </div>

                <div class="team-member" data-aos="flip-left" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80" class="member-img" alt="CMO">
                    <div class="member-info">
                        <h4>Lê Văn Minh</h4>
                        <p>Trưởng phòng CSKH</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. CTA SECTION --}}
    <section class="cta-section" data-aos="fade-up">
        <h2 style="font-size: 2.5rem; margin-bottom: 15px;">Sẵn Sàng Nâng Cấp Dế Yêu?</h2>
        <p style="font-size: 1.1rem; opacity: 0.8;">Đừng chần chừ, hàng ngàn ưu đãi đang chờ bạn khám phá ngay hôm nay.</p>
        <a href="{{ route('products.index') }}" class="btn-visual">MUA SẮM NGAY</a>
    </section>

</div>
@endsection

@section('scripts')
{{-- Script khởi tạo AOS Animation --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true, // Chỉ chạy animation 1 lần khi cuộn xuống
        offset: 100, // Kích hoạt khi cách đáy màn hình 100px
    });
</script>
@endsection