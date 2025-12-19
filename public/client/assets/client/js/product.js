
document.addEventListener('DOMContentLoaded', function () {
    
    // --- 1. LOGIC XEM THÊM/THU GỌN (MÔ TẢ SẢN PHẨM) ---
    const readMoreButtons = document.querySelectorAll('.read-more-btn');
    const MAX_HEIGHT = 200; // Chiều cao giới hạn trong CSS

    readMoreButtons.forEach(button => {
        const targetId = button.getAttribute('data-target');
        const contentWrapper = document.querySelector(`#${targetId} .content-limit`);

        // Kiểm tra và ẩn nút nếu nội dung không đủ dài
        // Phải kiểm tra sau khi nội dung đã tải
        if (contentWrapper && contentWrapper.scrollHeight > MAX_HEIGHT + 10) { 
            button.addEventListener('click', function() {
                const isExpanded = contentWrapper.classList.contains('expanded');
                if (isExpanded) {
                    contentWrapper.classList.remove('expanded');
                    button.textContent = 'Xem thêm';
                } else {
                    contentWrapper.classList.add('expanded');
                    button.textContent = 'Thu gọn';
                }
            });
        } else if (contentWrapper) {
                button.style.display = 'none'; // Ẩn nút nếu không cần thiết
        }
    });
    
    // Xử lý chuyển tab
    const tabNavItems = document.querySelectorAll('.tab-nav li');
    tabNavItems.forEach(navItem => {
        navItem.addEventListener('click', function() {
            // Đổi active tab
            document.querySelector('.tab-nav li.active').classList.remove('active');
            this.classList.add('active');

            // Ẩn/Hiện nội dung tab
            const targetTabId = this.getAttribute('data-tab');
            document.querySelector('.tab-content.active').classList.remove('active');
            document.getElementById(targetTabId).classList.add('active');

            // Thu gọn tất cả nội dung MÔ TẢ khi chuyển tab
            document.querySelectorAll('.content-limit.expanded').forEach(content => {
                content.classList.remove('expanded');
            });
            document.querySelectorAll('.read-more-btn').forEach(btn => {
                btn.textContent = 'Xem thêm';
            });
        });
    });

    // --- 2. LOGIC MODAL THÔNG SỐ CHI TIẾT ---
    const modal = document.getElementById('detailedSpecsModal');
    const openBtn = document.getElementById('openDetailedSpecsModal');
    const closeBtn = modal.querySelector('.close-btn');

    // Mở Modal
    if (openBtn) {
        openBtn.onclick = function() {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden'; // Ngăn cuộn nền
        }
    }

    // Đóng Modal khi nhấn nút X
    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.classList.remove('open');
            document.body.style.overflow = ''; 
        }
    }

    // Đóng Modal khi click ra ngoài
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // ----------------------------------------------------------------------
    // 1. LOGIC XỬ LÝ NÚT GIỎ HÀNG & MUA NGAY
    // ----------------------------------------------------------------------
    const productDetailContainer = document.querySelector('.product-detail-container');
    const productId = productDetailContainer ? productDetailContainer.getAttribute('data-product-id') : null;
    
    const buyNowBtn = document.querySelector('.buy-now-btn');
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    const quantityInput = document.querySelector('.quantity-input');
    const variantButtons = document.querySelectorAll('.variant-btn');

    // Khởi tạo Variant ID từ biến thể active/mặc định
    let selectedVariantId = document.querySelector('.variant-btn.active')?.getAttribute('data-variant-id');

    // Hàm cập nhật biến thể ID khi click
    variantButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Loại bỏ active khỏi tất cả
            document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
            // Thêm active vào nút được click
            this.classList.add('active');
            // Cập nhật Variant ID
            selectedVariantId = this.getAttribute('data-variant-id');
            
            // TODO: Cập nhật giá hiển thị trên giao diện nếu cần
            // const currentPrice = this.getAttribute('data-price');
            // document.querySelector('.current-price-display').textContent = formatPrice(currentPrice);
        });
    });

    // Hàm chung xử lý thêm sản phẩm vào giỏ hàng
    async function handleAddToCart(actionType) {
        if (!productId || !selectedVariantId) {
            alert('Lỗi: Vui lòng chọn một phiên bản sản phẩm.');
            return;
        }

        const quantity = parseInt(quantityInput.value, 10);
        if (quantity < 1) {
            alert('Số lượng phải lớn hơn 0.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'add_to_cart');
        formData.append('product_id', productId);
        formData.append('variant_id', selectedVariantId);
        formData.append('quantity', quantity);
        
        // URL tới tệp xử lý PHP của bạn
        const handlerUrl = '/MobileShop/app/api/cart_handler.php';
        
        try {
            const response = await fetch(handlerUrl, { 
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (actionType === 'cart') {
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                    // TODO: Cập nhật số lượng giỏ hàng trên Header (nếu có)
                } else if (actionType === 'buy') {
                    // Chuyển hướng đến trang thanh toán ngay lập tức
                    window.location.href = '/MobileShop/checkout'; 
                }
            } else {
                alert('Lỗi: ' + (result.message || 'Không thể thêm sản phẩm.'));
            }

        } catch (error) {
            console.error('Lỗi khi gửi yêu cầu:', error);
            alert('Lỗi kết nối đến server. Vui lòng kiểm tra đường dẫn file cart_handler.php');
        }
    }

    // Gán sự kiện cho nút "Giỏ hàng"
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            handleAddToCart('cart');
        });
    }

    // Gán sự kiện cho nút "Mua ngay"
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', () => {
            handleAddToCart('buy');
        });
    }
    
    // ----------------------------------------------------------------------
    // 2. LOGIC TABS & MODAL (Đã có từ các câu trả lời trước)
    // ... Đảm bảo các logic này vẫn ở đây ...
    // ----------------------------------------------------------------------
});

document.addEventListener('DOMContentLoaded', function() {
    const variantButtons = document.querySelectorAll('.variant-btn');
    const quantityInput = document.getElementById('quantityInput');

    if (!quantityInput) return; // Đảm bảo input số lượng tồn tại

    /**
     * Hàm cập nhật giới hạn tối đa cho input số lượng
     * @param {number} maxQuantity Số lượng tồn kho tối đa
     */
    function updateQuantityMax(maxQuantity) {
        // Cập nhật thuộc tính 'max' của input
        quantityInput.setAttribute('max', maxQuantity);

        // Đảm bảo giá trị hiện tại không vượt quá giới hạn mới
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > maxQuantity) {
            quantityInput.value = maxQuantity;
        }
        
        // Cập nhật trạng thái hiển thị (Ví dụ: báo hết hàng)
        if (maxQuantity < 1) {
             // Ví dụ: Disable nút mua hàng nếu hết hàng
             document.querySelector('.buy-now-btn').disabled = true;
             document.querySelector('.add-to-cart-btn').disabled = true;
             console.log("Sản phẩm đã hết hàng.");
        } else {
             document.querySelector('.buy-now-btn').disabled = false;
             document.querySelector('.add-to-cart-btn').disabled = false;
        }
    }

    // === 1. Lắng nghe sự kiện click trên các nút biến thể ===
    variantButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Xóa class active khỏi tất cả nút
            variantButtons.forEach(btn => btn.classList.remove('active'));
            // Thêm class active vào nút hiện tại
            this.classList.add('active');

            // Lấy số lượng tồn kho từ data attribute
            const maxQuantity = parseInt(this.dataset.quantity);
            
            // Cập nhật giới hạn số lượng
            updateQuantityMax(maxQuantity);
            
            // NOTE: Cần thêm logic cập nhật giá sản phẩm ở đây
        });
    });

    // === 2. Khởi tạo giá trị khi tải trang lần đầu ===
    const defaultActiveButton = document.querySelector('.variant-btn.active');
    if (defaultActiveButton) {
        const initialMaxQuantity = parseInt(defaultActiveButton.dataset.quantity);
        updateQuantityMax(initialMaxQuantity);
    } 
    // Nếu không có biến thể nào được chọn mặc định, đặt max là 0
    else {
        updateQuantityMax(0); 
    }

    // === 3. Ngăn chặn nhập số lượng thủ công vượt quá giới hạn ===
    quantityInput.addEventListener('change', function() {
        let max = parseInt(this.getAttribute('max'));
        let min = parseInt(this.getAttribute('min'));
        let value = parseInt(this.value);

        if (value > max) {
            this.value = max;
        } else if (value < min || isNaN(value)) {
            this.value = min;
        }
    });

    const addToCartButton = document.querySelector('.add-to-cart-btn');
    const productDetailContainer = document.querySelector('.product-detail-container');
    
    if (!addToCartButton || !quantityInput || !productDetailContainer) {
        // Đảm bảo tất cả các phần tử cần thiết tồn tại
        console.error('Missing required elements for Add To Cart logic.');
        return;
    }

    addToCartButton.addEventListener('click', function() {
        // 1. Lấy ID biến thể đang được chọn (active)
        const activeVariantButton = document.querySelector('.variant-btn.active');
        if (!activeVariantButton) {
            alert('Vui lòng chọn một phiên bản/màu sắc.');
            return;
        }

        const variantId = activeVariantButton.dataset.variantId;
        const quantity = parseInt(quantityInput.value, 10);
        const maxQuantity = parseInt(quantityInput.getAttribute('max'), 10);

        // 2. Kiểm tra số lượng hợp lệ
        if (quantity < 1 || isNaN(quantity)) {
            alert('Số lượng phải lớn hơn 0.');
            return;
        }
        if (quantity > maxQuantity) {
            alert(`Rất tiếc, số lượng tối đa bạn có thể đặt là ${maxQuantity}.`);
            return;
        }

        // 3. Chuẩn bị dữ liệu gửi (Form Data)
        const formData = new FormData();
        formData.append('variant_id', variantId);
        formData.append('quantity', quantity);

        // 4. Gửi yêu cầu qua Fetch API (AJAX)
        fetch('/MobileShop/cart/add', { // Đây là endpoint bạn cần định tuyến trong PHP
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Giả sử Controller trả về JSON
        .then(data => {
            if (data.success) {
                alert('Sản phẩm đã được thêm vào giỏ hàng thành công!');
                // Cập nhật số đếm giỏ hàng trên header nếu có
                const badge = document.getElementById('cartCountBadge');
                if (badge) {
                    badge.textContent = data.newCartCount; // Lấy số lượng mới từ Controller
                }
            } else {
                // Hiển thị lỗi nếu có (ví dụ: tồn kho không đủ)
                alert('Lỗi: ' + (data.message || 'Không thể thêm vào giỏ hàng.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi hệ thống.');
        });
    });

    // Cần đảm bảo logic sau đây chạy đúng để cập nhật MAX
    
    // Hàm cập nhật maxQuantity (giữ nguyên từ logic cũ)
    function updateQuantityMax(maxQuantity) {
        quantityInput.setAttribute('max', maxQuantity);
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > maxQuantity) {
            quantityInput.value = maxQuantity;
        }
    }

    variantButtons.forEach(button => {
        button.addEventListener('click', function() {
            // ... (Logic chọn active button và cập nhật giá) ...
            
            const stockQuantity = parseInt(this.dataset.quantity, 10);
            updateQuantityMax(stockQuantity);
            
            // Cập nhật variant_id trong data-attribute của container (nếu cần cho các logic khác)
            productDetailContainer.dataset.currentVariantId = this.dataset.variantId;
        });
    });

    // Khởi tạo max ban đầu
    const initialMax = parseInt(productDetailContainer.dataset.currentVariantId ? 
                                document.querySelector(`.variant-btn.active`).dataset.quantity : 
                                quantityInput.getAttribute('max'), 10);
    updateQuantityMax(initialMax);

});

