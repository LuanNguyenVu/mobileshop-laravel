<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MobileShop')</title>
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/layouts.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('css')
    <script src="{{ asset('client/assets/client/js/main.js') }}"></script>
    @stack('scripts')
</head>
<body>

    <header class="main-header">
        <div class="container">
            <div class="header-inner">
                <div class="logo">
                    <a href="/MobileShop">
                        <i class="fas fa-mobile-alt"></i>
                        <span>MOBILESHOP</span>
                    </a>
                </div>

                <div class="search-bar">
                    <form action="#" method="GET">
                        <input type="text" placeholder="Bạn tìm gì..." name="keyword">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <nav class="main-nav">
                    <a href="{{ route('home') }}">Trang chủ</a>
                    <a href="{{ route('about') }}">Giới thiệu</a>
                    <a href="{{ route('products.index') }}">Sản phẩm</a>
                    <a href="{{ route('news.index') }}">Tin tức</a>
                    <a href="{{ route('contact') }}">Liên hệ</a>
                    <a href="{{ route('cart.index') }}">Giỏ hàng</a>

                    @auth
                            <div class="user-menu-item">
                                <a href="#" class="user-toggle">
                                    <span>Xin chào, {{ Auth::user()->username }}</span>
                                    <i class="fas fa-caret-down"></i>
                                </a>

                                {{-- MENU THẢ XUỐNG --}}
                                <ul class="user-dropdown-menu">
                                    {{-- 1. Quản lý tài khoản --}}
                                    <li>
                                        <a href="{{ route('account.profile') }}">
                                            <i class="fas fa-user-circle"></i> Quản lý tài khoản
                                        </a>
                                    </li>

                                    {{-- 2. Quản lý đơn hàng --}}
                                    <li>
                                        <a href="{{ route('order_list') }}">
                                            <i class="fas fa-shopping-bag"></i> Quản lý đơn hàng
                                        </a>
                                    </li>

                                    {{-- 3. Đăng xuất --}}
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                            
                                            @csrf  <button type="submit" class="btn-logout-dropdown" style="background:none; border:none; padding:0; cursor:pointer; width:100%; text-align:left;">
                                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endauth

                    @guest
                        <a href="{{ route('login') }}">Đăng nhập</a>
                        <a href="{{ route('register') }}">Đăng ký</a>
                    @endguest
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="main-footer-light">
        <div class="footer-container">
            
            <div class="footer-col contact-col">
                <h4>Về Chúng Tôi</h4>
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>Hotline: (+84) 965 172009</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>Email: nguyenvuluan20060522@gmail.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Địa chỉ: 500 Điện Biên Phủ, Thanh Khê Đông, Thanh Khê, Đà Nẵng</span>
                </div>
            </div>

            <div class="footer-col links-col">
                <h4>Hỗ Trợ Khách Hàng</h4>
                <ul>
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Chính sách vận chuyển</a></li>
                    <li><a href="#">Chính sách đổi trả hàng</a></li>
                    <li><a href="#">Hướng dẫn thanh toán</a></li>
                </ul>
            </div>

            <div class="footer-col payment-col">
                <h4>Hỗ Trợ Thanh Toán</h4>
                <div class="payment-logos">
                    <img src="{{ asset('client/assets/client/images/placeholder-cards.png') }}" alt="Visa/Mastercard">
                    <img src="{{ asset('client/assets/client/images/placeholder-momo.png') }}" alt="Momo">
                </div>
            </div>

            <div class="footer-col social-col">
                <h4>Theo Dõi Chúng Tôi</h4>
                
                <div class="fb-page" 
                    data-href="YOUR_FACEBOOK_PAGE_URL" 
                    data-tabs="timeline" 
                    data-width="300" 
                    data-height="250" 
                    data-small-header="true" 
                    data-adapt-container-width="true" 
                    data-hide-cover="false" 
                    data-show-facepile="true">
                    <blockquote cite="https://www.facebook.com/reel/" class="fb-xfbml-parse-ignore">
                        <a href="https://www.facebook.com/reel/">Tên Fanpage Của Bạn</a>
                    </blockquote>
                </div>
                <div class="other-social-links">
                    <a href="YOUR_YOUTUBE_LINK" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="YOUR_ZALO_LINK" target="_blank" title="Zalo"><i class="fab fa-twitter"></i></a>
                    <a href="YOUR_INSTAGRAM_LINK" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

        </div>

        <div class="footer-bottom-light">
            <p>Copyright © 2025 Nguyễn Vũ Luân. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('client/assets/client/js/main.js') }}"></script>
    @stack('scripts') @yield('scripts') @section('scripts')
    {{-- === 1. ALERT THÔNG BÁO THÀNH CÔNG/LỖI === --}}
    @if(session('success'))
        <div class="custom-alert alert-success" id="success-alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="custom-alert alert-danger" id="error-alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- === 2. CSS CHO ALERT (Nên đưa vào file CSS riêng, nhưng để đây cho tiện) === --}}
    <style>
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.5s ease-out, fadeOut 0.5s ease-in 4.5s forwards;
            font-weight: 500;
            min-width: 300px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; visibility: hidden; }
        }
    </style>

    {{-- === 3. SCRIPT TỰ ĐỘNG ẨN SAU 5 GIÂY === --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.custom-alert');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 3000); // 3000ms = 3 giây
        });
    </script>
</body>
</html>