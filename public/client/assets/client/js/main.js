/* assets/js/main.js */
document.addEventListener('DOMContentLoaded', function () {
    // Simple search button focus
    const searchBtn = document.querySelector('.search-bar button');
    const searchInput = document.querySelector('.search-bar input[type="text"]');
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function () {
            const q = searchInput.value.trim();
            if (!q) {
                searchInput.focus();
                return;
            }
            // change this behavior to your search route if needed
            window.location.href = '/?q=' + encodeURIComponent(q);
        });
    }

    // Register form password confirmation check
    const regForm = document.querySelector('form[action="/register-post"]') || document.querySelector('form[action="register-post"]');
    if (regForm) {
        regForm.addEventListener('submit', function (e) {
            const p = regForm.querySelector('input[name="password"]');
            const pc = regForm.querySelector('input[name="password_confirmation"]') || regForm.querySelector('input[name="password_confirmation"]');
            if (p && pc && p.value !== pc.value) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp. Vui lòng kiểm tra lại.');
                p.focus();
                return false;
            }
            return true;
        });
    }

    // Auto-hide flash / error messages (class .flash or .error)
    setTimeout(function () {
        document.querySelectorAll('.flash, .error, .success').forEach(el => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4500);
});

