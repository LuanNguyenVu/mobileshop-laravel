// Nội dung file: app/assets/js/slider.js

document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.querySelector('.slider-container');
    const slides = document.querySelectorAll('.slide-item');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const navItems = document.querySelectorAll('.nav-item'); // Lấy các tiêu đề nav
    
    const totalSlides = slides.length;
    let currentIndex = 0;
    let autoSlideInterval;

    if (totalSlides <= 1) {
        // Ẩn nút và nav nếu chỉ có 1 slide
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        if (navItems.length > 0) {
            document.querySelector('.slider-navigation').style.display = 'none';
        }
        return; 
    }

    // --- Hàm Core ---
    function updateNavigation() {
        // Xóa class 'active' khỏi tất cả nav items
        navItems.forEach(item => item.classList.remove('active'));
        // Thêm class 'active' cho nav item hiện tại
        if (navItems[currentIndex]) {
            navItems[currentIndex].classList.add('active');
        }
    }

    function goToSlide(index) {
        // Xóa interval cũ trước khi chuyển
        clearInterval(autoSlideInterval);
        
        // Đảm bảo chỉ số nằm trong phạm vi
        if (index >= totalSlides) {
            index = 0;
        } else if (index < 0) {
            index = totalSlides - 1;
        }
        currentIndex = index;
        
        // Cập nhật vị trí slider
        const offset = -currentIndex * 100;
        sliderContainer.style.transform = `translateX(${offset}%)`;

        // Cập nhật thanh điều hướng
        updateNavigation();

        // Khởi động lại auto-slide sau khi chuyển xong
        startAutoSlide();
    }

    function startAutoSlide() {
        // Thiết lập tự động chuyển slide sau 5 giây
        autoSlideInterval = setInterval(function() {
            goToSlide(currentIndex + 1);
        }, 5000); 
    }

    // --- Xử lý Sự kiện ---

    // 1. Xử lý nút Next/Previous
    nextBtn.addEventListener('click', function() {
        goToSlide(currentIndex + 1);
    });

    prevBtn.addEventListener('click', function() {
        goToSlide(currentIndex - 1);
    });

    // 2. Xử lý click vào Nav Item
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));
            goToSlide(index);
        });
    });

    // 3. Dừng/Tiếp tục Auto-Slide khi hover
    sliderContainer.parentElement.addEventListener('mouseenter', function() {
        clearInterval(autoSlideInterval);
    });
    
    sliderContainer.parentElement.addEventListener('mouseleave', function() {
        startAutoSlide();
    });

    // --- Khởi tạo ---
    goToSlide(0);
    startAutoSlide();
});