@extends('layouts.app')

@section('title', $product->product_name)

@push('css')
    <link rel="stylesheet" href="{{ asset('client/assets/client/css/products.css') }}">
@endpush

@section('content')
<div class="container product-detail-container">

    {{-- PHẦN 1: ẢNH - THÔNG TIN - HỖ TRỢ --}}
    <div class="product-info-grid">
        <div class="image-gallery">
            <img src="{{ asset($product->product_image) }}" 
                 alt="{{ $product->product_name }}" 
                 class="main-image">
        </div>

        <div class="product-details">
            <h1 class="product-title">{{ $product->product_name }}</h1>
            
            <div class="rating">
                @for($i = 1; $i <= 5; $i++)
                    @if($product->rating >= $i) <i class="fas fa-star checked" style="color:#ffc107"></i>
                    @elseif($product->rating > $i-1) <i class="fas fa-star-half-alt checked" style="color:#ffc107"></i>
                    @else <i class="far fa-star" style="color:#ccc"></i>
                    @endif
                @endfor
                <span style="color:#999;">({{ number_format($product->rating, 1) }})</span>
            </div>

            <p class="status">Tình trạng: 
                <span style="font-weight: bold; color: {{ $product->status == 'in_stock' ? 'green' : 'red' }}">
                    {{ $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}
                </span>
            </p>

            {{-- Logic chọn giá mặc định (Biến thể đầu tiên hoặc rẻ nhất) --}}
            @php
                $current_variant = $product->variants->sortBy('selling_price')->first();
                $selling_price = $current_variant->selling_price ?? 0;
                $promotional_price = $current_variant->promotional_price ?? 0;
                $is_promo = ($promotional_price > 0 && $promotional_price < $selling_price);
                $current_price = $is_promo ? $promotional_price : $selling_price;
            @endphp

            <div class="price-box">
                <span class="current-price-display" id="display-price">
                    {{ number_format($current_price, 0, ',', '.') }}₫
                </span>
                
                <span class="old-price-display" id="display-old-price" style="{{ !$is_promo ? 'display:none' : '' }}">
                    {{ number_format($selling_price, 0, ',', '.') }}₫
                </span>
                
                <span class="discount-label" id="display-discount" style="{{ !$is_promo ? 'display:none' : '' }}">
                    Giảm {{ number_format($selling_price - $promotional_price, 0, ',', '.') }}₫
                </span>
            </div>

            <hr>

            <div class="variants-section">
                <h3>Chọn Phiên Bản/Màu Sắc:</h3>
                <div class="color-options">
                    @foreach($product->variants as $index => $variant)
                        {{-- Lấy số lượng đã có trong giỏ của biến thể này --}}
                        @php
                            $inCartQty = $cartQuantities[$variant->id] ?? 0;
                        @endphp

                        <button class="variant-btn {{ $variant->id == $current_variant->id ? 'active' : '' }}" 
                            data-id="{{ $variant->id }}"
                            data-price="{{ $variant->promotional_price > 0 ? $variant->promotional_price : $variant->selling_price }}"
                            data-old-price="{{ $variant->selling_price }}"
                            data-promo="{{ $variant->promotional_price > 0 ? 1 : 0 }}"
                            
                            {{-- THÊM 2 DÒNG QUAN TRỌNG NÀY --}}
                            data-stock="{{ $variant->quantity }}" 
                            data-incart="{{ $inCartQty }}">
                            
                            {{ $variant->color }} 
                            @if(isset($variant->ram) && isset($variant->rom))
                                <small>({{ $variant->ram }}/{{ $variant->rom }})</small>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="action-buttons">
                {{-- Lấy số lượng của biến thể đầu tiên --}}
                @php
                    $maxQty = $current_variant ? $current_variant->quantity : 1;
                @endphp
                <input type="number" value="1" min="1" max="{{ $maxQty }}" class="quantity-input" id="quantityInput">
                
                {{-- Form Mua Ngay (chuyển thẳng đến Checkout - sẽ làm sau) --}}
                <button class="buy-now-btn">
                    <i class="fas fa-shopping-bag"></i> Mua ngay
                </button>
                
                {{-- Nút Thêm vào giỏ (Sử dụng Ajax hoặc Form) --}}
                <button class="add-to-cart-btn"> <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
            </div>
        </div>
        <div class="support-card">
            <div class="support-header">CHÚNG TÔI LUÔN SẴN SÀNG</div>
            {{-- Sửa đường dẫn ảnh support --}}
            <img src="{{ asset('client/assets/client/images/support.jpg') }}" 
                alt="Support" style="max-width:100%; margin: 15px 0;">
            <p>Để được hỗ trợ tốt nhất. Hãy gọi</p>
            <a href="tel:18006750" class="support-phone">1800 6750</a>
            <p>HOẶC</p>
            <a href="#" style="color:#e44d26; font-weight:bold; text-decoration:underline;">Chat hỗ trợ</a>
        </div>
    </div>

    {{-- PHẦN 2: THÔNG SỐ & MÔ TẢ --}}
    <div style="display: flow-root;"> <div class="specs-summary">
            <h3>THÔNG SỐ KỸ THUẬT</h3>
            <table>
                <tr><td>Màn Hình:</td><td>{{ $product->screen ?? 'Đang cập nhật' }}</td></tr>
                <tr><td>Cam Trước:</td><td>{{ $product->front_camera ?? 'Đang cập nhật' }}</td></tr>
                <tr><td>Cam Sau:</td><td>{{ $product->camera ?? 'Đang cập nhật' }}</td></tr>
                <tr><td>Chipset:</td><td>{{ $product->cpu ?? 'Đang cập nhật' }}</td></tr>
                <tr><td>RAM:</td><td>{{ $product->ram }}</td></tr>
                <tr><td>Bộ nhớ:</td><td>{{ $product->rom }}</td></tr>
                <tr><td>Pin:</td><td>{{ $product->battery }}</td></tr>
                <tr><td>Hệ điều hành:</td><td>{{ $product->operating_system ?? 'Đang cập nhật' }}</td></tr>
            </table>
            <button class="btn-detail-specs" id="openModalBtn">Xem cấu hình chi tiết</button>
        </div>

        <div class="product-tabs">
            <ul class="tab-nav">
                <li class="active" data-tab="description">MÔ TẢ</li>
                <li data-tab="reviews">ĐÁNH GIÁ</li>
            </ul>

            <div id="description" class="tab-content active">
                <div class="article-content">
                    {{-- Dùng {!! !!} để hiển thị HTML từ database --}}
                    {!! $product->description ?? '<p>Đang cập nhật mô tả sản phẩm...</p>' !!}
                </div>
            </div>
            
<div id="reviews" class="tab-content">
    <div class="reviews-container">
        
        {{-- Form viết đánh giá (Chỉ hiện khi đã đăng nhập) --}}
        @auth
            <div class="review-form-box">
                <h4>Viết đánh giá của bạn</h4>
                <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                    @csrf
                    <div class="star-rating-input">
                        <input type="radio" id="star5" name="rating" value="5" checked /><label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                    </div>
                    <textarea name="comment" class="review-textarea" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                    <button type="submit" class="btn-submit-review">Gửi Đánh Giá</button>
                </form>
            </div>
        @else
            <div class="alert alert-warning" style="background: #fff3cd; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                Vui lòng <a href="{{ route('login') }}" style="color: #856404; font-weight: bold; text-decoration: underline;">đăng nhập</a> để viết đánh giá.
            </div>
        @endauth

            <div id="reviews" class="tab-content">
                <div class="reviews-container">
                    
                    {{-- Form viết đánh giá (Chỉ hiện khi đã đăng nhập) --}}
                    @auth
                        <div class="review-form-box">
                            <h4>Viết đánh giá của bạn</h4>
                            <form action="{{ route('reviews.store', $product->id) }}" method="POST">
                                @csrf
                                <div class="star-rating-input">
                                    <input type="radio" id="star5" name="rating" value="5" checked /><label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                                </div>
                                <textarea name="comment" class="review-textarea" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                                <button type="submit" class="btn-submit-review">Gửi Đánh Giá</button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-warning" style="background: #fff3cd; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                            Vui lòng <a href="{{ route('login') }}" style="color: #856404; font-weight: bold; text-decoration: underline;">đăng nhập</a> để viết đánh giá.
                        </div>
                    @endauth

                    {{-- Danh sách đánh giá --}}
                    <div class="review-list">
                        @if($product->reviews->isEmpty())
                            <p style="color: #666; font-style: italic;">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                        @else
                            @foreach($product->reviews as $review)
                                <div class="review-item">
                                    <div class="review-user">
                                        <img src="{{ $review->user->avatar_path ? asset($review->user->avatar_path) : 'https://ui-avatars.com/api/?name='.$review->user->username }}" alt="User">
                                        <span>{{ $review->user->username }}</span>
                                    </div>
                                    <div class="review-stars">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="review-content">{{ $review->comment }}</p>
                                    <div class="review-date">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CẤU HÌNH CHI TIẾT --}}
    <div class="modal-overlay" id="specsModal">
        <div class="modal-content">
            <span class="close-btn" id="closeModalBtn">&times;</span>
            <h2>Thông Số Kỹ Thuật Chi Tiết</h2>
            <div class="modal-body">
                {!! $product->detailed_specs ?? '<p>Chưa có thông tin chi tiết.</p>' !!}
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- KHAI BÁO ---
        const variantBtns = document.querySelectorAll('.variant-btn');
        const addToCartBtn = document.querySelector('.add-to-cart-btn');
        const buyNowBtn = document.querySelector('.buy-now-btn');
        const quantityInput = document.getElementById('quantityInput');
        
        // Lấy Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Lấy ID biến thể mặc định (Nếu sản phẩm có biến thể đầu tiên)
        // Lưu ý: PHP sẽ in ra ID vào đây. Nếu rỗng nghĩa là chưa chọn.
        let selectedVariantId = "{{ $current_variant->id ?? '' }}"; 
        
        console.log('ID ban đầu:', selectedVariantId); // Kiểm tra F12 xem có ID chưa

        // --- 2. XỬ LÝ CHỌN MÀU SẮC ---
        variantBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // 1. UI Active
                variantBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // 2. Cập nhật thông tin cơ bản
                const price = parseFloat(this.dataset.price);
                const oldPrice = parseFloat(this.dataset.oldPrice);
                const isPromo = this.dataset.promo == "1";
                selectedVariantId = this.dataset.id;

                // --- 3. LOGIC TÍNH MAX SỐ LƯỢNG (MỚI) ---
                const totalStock = parseInt(this.dataset.stock);   // Tổng kho
                const inCart = parseInt(this.dataset.incart);      // Đã có trong giỏ
                
                // Số lượng CÒN LẠI được phép mua
                let remainingAllowed = totalStock - inCart;
                if (remainingAllowed < 0) remainingAllowed = 0;

                const qtyInput = document.getElementById('quantityInput');
                const addToCartBtn = document.querySelector('.add-to-cart-btn');
                const buyNowBtn = document.querySelector('.buy-now-btn');

                // Cập nhật thuộc tính max cho ô input
                qtyInput.max = remainingAllowed;
                
                // Nếu còn hàng để mua
                if (remainingAllowed > 0) {
                    qtyInput.disabled = false;
                    addToCartBtn.disabled = false;
                    buyNowBtn.disabled = false;
                    addToCartBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng';
                    
                    // Nếu đang nhập số lớn hơn số cho phép -> reset về max
                    if (parseInt(qtyInput.value) > remainingAllowed) {
                        qtyInput.value = remainingAllowed;
                    }
                    // Nếu input đang là 0 hoặc thấp hơn -> reset về 1
                    if (parseInt(qtyInput.value) < 1) {
                        qtyInput.value = 1;
                    }
                } else {
                    // Hết quota mua hàng (đã mua hết số lượng trong kho)
                    qtyInput.value = 0;
                    qtyInput.disabled = true;
                    addToCartBtn.disabled = true;
                    buyNowBtn.disabled = true;
                    addToCartBtn.innerHTML = '🚫 Đã đạt giới hạn kho';
                }

                // 4. Cập nhật giá hiển thị
                priceDisplay.innerText = new Intl.NumberFormat('vi-VN').format(price) + '₫';
                if (isPromo && oldPrice > price) {
                    oldPriceDisplay.style.display = 'inline';
                    oldPriceDisplay.innerText = new Intl.NumberFormat('vi-VN').format(oldPrice) + '₫';
                    discountDisplay.style.display = 'inline';
                    discountDisplay.innerText = 'Giảm ' + new Intl.NumberFormat('vi-VN').format(oldPrice - price) + '₫';
                } else {
                    oldPriceDisplay.style.display = 'none';
                    discountDisplay.style.display = 'none';
                }
                
                // Debug
                console.log(`Kho: ${totalStock}, Trong giỏ: ${inCart}, Còn lại: ${remainingAllowed}`);
            });
            
            // Kích hoạt click vào nút đầu tiên để chạy logic check kho ngay khi load trang
            if(btn.classList.contains('active')) {
                btn.click();
            }
        });

        // Thêm sự kiện chặn nhập tay quá số lượng
        document.getElementById('quantityInput').addEventListener('input', function() {
            const max = parseInt(this.max);
            if (parseInt(this.value) > max) {
                this.value = max; // Tự động sửa về max nếu nhập lố
            }
        });

        // --- HÀM GỬI AJAX ---
        function processCart(actionType) {
            if (!csrfToken) {
                alert('Lỗi: Không tìm thấy CSRF Token.');
                return;
            }

            if (!selectedVariantId) {
                alert('Vui lòng chọn màu sắc/phiên bản sản phẩm!');
                return;
            }

            const qty = quantityInput.value;
            const url = actionType === 'buy_now' ? '{{ route("cart.buyNow") }}' : '{{ route("cart.add") }}';
            const btn = actionType === 'buy_now' ? buyNowBtn : addToCartBtn;
            
            // Loading
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Đang xử lý...';
            btn.disabled = true;

            // Gửi Request
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    variant_id: selectedVariantId,
                    quantity: qty
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                console.log('Server trả về:', data); // Xem log kết quả

                if (data.status === 'success') {
                    if (actionType === 'buy_now') {
                        window.location.href = data.redirect;
                    } else {
                        if(confirm(data.message + '\nĐến giỏ hàng ngay?')) {
                            window.location.href = '{{ route("cart.index") }}';
                        }
                    }
                } else {
                    alert(data.message); // Hiển thị lỗi từ server (ví dụ: chưa đăng nhập)
                    if(data.message.includes('đăng nhập')) {
                        window.location.href = '{{ route("login") }}';
                    }
                }
            })
            .catch(error => {
                console.error('Lỗi AJAX:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Có lỗi xảy ra. Xem Console (F12) để biết chi tiết.');
            });
        }

        // --- GÁN SỰ KIỆN CLICK ---
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Chặn load lại trang
                processCart('add');
            });
        }

        if (buyNowBtn) {
            buyNowBtn.addEventListener('click', function(e) {
                e.preventDefault();
                processCart('buy_now');
            });
        }
        // --- 3. XỬ LÝ MODAL CẤU HÌNH ---
        const modal = document.getElementById('specsModal');
        const openBtn = document.getElementById('openModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');

        if (openBtn && modal && closeBtn) {
            openBtn.addEventListener('click', function() {
                modal.style.display = 'flex'; // Hiện modal
            });

            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none'; // Ẩn modal
            });

            // Click ra ngoài vùng trắng cũng tắt modal
            window.addEventListener('click', function(e) {
                if (e.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // --- 4. XỬ LÝ TAB (MÔ TẢ / ĐÁNH GIÁ) ---
        const tabs = document.querySelectorAll('.tab-nav li');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Xóa active cũ
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                // Thêm active mới
                this.classList.add('active');
                const targetId = this.dataset.tab;
                document.getElementById(targetId).classList.add('active');
            });
        });
    });
</script>
@endsection