<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest; 
use Illuminate\Http\Request;

// --- CLIENT CONTROLLERS ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ForgotPasswordController; // Controller quên mật khẩu

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController; 
use App\Http\Controllers\Admin\OrderController as AdminOrderController;     
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RevenueController;

// ====================================================
// 1. KHÁCH (GUEST) - KHÔNG CẦN ĐĂNG NHẬP
// ====================================================

// Trang chủ & Thông tin
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

// Sản phẩm & Tin tức
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Đăng nhập & Đăng ký
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Quên mật khẩu (Password Reset)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// ====================================================
// 2. ĐĂNG NHẬP NHƯNG CHƯA CẦN KÍCH HOẠT EMAIL
// ====================================================
Route::middleware(['auth'])->group(function () {

    // --- CÁC ROUTE XỬ LÝ XÁC THỰC EMAIL ---
    // 1. Trang thông báo "Vui lòng check mail"
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // 2. Link xác thực trong email bấm vào sẽ chạy route này
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // Đánh dấu đã xác thực
        return redirect()->route('home')->with('success', 'Email đã được xác thực thành công!');
    })->middleware('signed')->name('verification.verify');

    // 3. Gửi lại email xác thực
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link xác thực đã được gửi lại!');
    })->middleware('throttle:6,1')->name('verification.send');
});


// ====================================================
// 3. ĐĂNG NHẬP + ĐÃ KÍCH HOẠT EMAIL (ACTIVE)
// ====================================================
// Những route này BẮT BUỘC phải verify email mới vào được.
// Nếu chưa verify, sẽ bị đá về trang verification.notice
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Giỏ hàng (Cart)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');

    // Thanh toán (Checkout)
    Route::get('/checkout', [OrderController::class, 'index'])->name('checkout.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');

    // Đơn hàng & Đánh giá
    Route::get('/my-orders', [OrderController::class, 'index'])->name('order_list');
    Route::get('/order-detail/{orderCode}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/products/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Quản lý tài khoản cá nhân
    Route::get('/account/profile', [AccountController::class, 'index'])->name('account.profile');
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update');
    Route::post('/account/upload-avatar', [AccountController::class, 'uploadAvatar'])->name('account.upload_avatar');
});


// ====================================================
// 4. QUẢN TRỊ VIÊN (ADMIN ROUTES)
// ====================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin') // Chỉ Guard Admin mới được vào
    ->group(function () {
        
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý tài nguyên (CRUD)
    Route::resource('ads', AdController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('orders', AdminOrderController::class);
    Route::resource('users', UserController::class);
    Route::resource('posts', PostController::class);

    // Thống kê
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/products/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');
});