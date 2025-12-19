@extends('layouts.app')

@section('title', 'Liên Hệ - Mobile Shop')

@push('css')
<link rel="stylesheet" href="{{ asset('client/assets/client/css/contact.css') }}">
@endpush

@section('content')
<div class="contact-page">

    {{-- 1. HERO BANNER --}}
    <section class="contact-hero">
        <div data-aos="fade-down">
            <h1>Liên Hệ Với Chúng Tôi</h1>
            <p>Chúng tôi luôn lắng nghe và sẵn sàng hỗ trợ bạn 24/7</p>
        </div>
    </section>

    {{-- 2. FLOATING INFO CARDS --}}
    <div class="info-cards-container">
        <div class="info-grid">
            <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Địa Chỉ Cửa Hàng</h3>
                <p>500 Điện Biên Phủ, Thanh Khê Đông, Thanh Khê, Đà Nẵng</p>
                <a href="https://maps.google.com" target="_blank" style="color:#cc0000; font-weight:600; margin-top:5px; display:inline-block;">Chỉ đường ngay &rarr;</a>
            </div>

            <div class="info-card" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
                <h3>Hotline Hỗ Trợ</h3>
                <p>Tư vấn bán hàng: <strong>1800 6750</strong></p>
                <p>Hỗ trợ kỹ thuật: <strong>1900 6750</strong></p>
                <p>Mở cửa: 8:00 - 22:00 (Cả CN & Lễ)</p>
            </div>

            <div class="info-card" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box"><i class="fas fa-envelope"></i></div>
                <h3>Email Liên Hệ</h3>
                <p>Gửi mail cho chúng tôi:</p>
                <p><strong>support@mobileshop.com.vn</strong></p>
                <p><strong>cskh@mobileshop.com.vn</strong></p>
            </div>
        </div>
    </div>

    {{-- 3. MAIN CONTENT: FORM & MAP --}}
    <div class="main-content">
        
        <div class="contact-form-wrapper" data-aos="fade-right">
            <h2 class="form-title">Gửi Tin Nhắn Cho Chúng Tôi</h2>
            
            @if(session('success'))
                <div class="alert alert-success" style="padding:15px; background:#d4edda; color:#155724; border-radius:6px; margin-bottom:20px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label style="font-weight:600; margin-bottom:5px; display:block;">Họ và tên</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên của bạn" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label style="font-weight:600; margin-bottom:5px; display:block;">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:600; margin-bottom:5px; display:block;">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="09xx xxx xxx" required>
                    </div>
                </div>

                <div class="form-group">
                    <label style="font-weight:600; margin-bottom:5px; display:block;">Nội dung cần hỗ trợ</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="Bạn cần chúng tôi giúp gì?" required></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Gửi Ngay
                </button>
            </form>
        </div>

        <div class="sidebar-contact">
            
            <div class="map-wrapper" data-aos="fade-left">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3833.896740682855!2d108.1966573759281!3d16.06994113940176!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314218dc4e883259%3A0x6b47c462319c5c2a!2zNTAwIMSQaeG7h24gQmnDqm4gUGjhu6csIFRoYW5oIEtow6ogxJDDtG5nLCBUaGFuaCBLaMOqLCDEkMOgIE7hurVuZyA1NTAwMDAsIFZpZXRuYW0!5e0!3m2!1sen!2s!4v1715694389000!5m2!1sen!2s" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="support-card" data-aos="fade-up" data-aos-delay="200">
                <div style="font-weight: bold; font-size: 1.1rem; letter-spacing: 1px; margin-bottom: 10px;">TRUNG TÂM CSKH</div>
                <img src="{{ asset('assets/client/images/support.jpg') }}" onerror="this.src='https://via.placeholder.com/300x200?text=Support+Team'" alt="Support Team">
                <p>Gặp trực tiếp tư vấn viên chuyên nghiệp</p>
                <a href="tel:18006750" class="support-phone">1800 6750</a>
                <a href="#" style="color:#ddd; text-decoration:underline;">Chat trực tuyến ngay &rarr;</a>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        duration: 800,
    });
</script>
@endsection