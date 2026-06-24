<?php

//use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\Host\HostController;
use App\Http\Controllers\Host\HostTourController;
use App\Http\Controllers\Host\HostBookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminApprovalController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AIChatController;

/*
|--------------------------------------------------------------------------
| Đăng nhập riêng cho Admin & Host (không cần đăng xuất user)
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [RoleLoginController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [RoleLoginController::class, 'adminLogin'])->name('admin.login.submit');
Route::get('/host/login', [RoleLoginController::class, 'showHostLogin'])->name('host.login');
Route::post('/host/login', [RoleLoginController::class, 'hostLogin'])->name('host.login.submit');

Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/host', function () {
    return redirect()->route('host.dashboard');
});

/*
|--------------------------------------------------------------------------
| Phân hệ 1: Khách du lịch (Tourist Portal) - Public & Protected
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PaymentController;

// Các trang Public không cần đăng nhập
Route::get('/', [HomeController::class, 'index'])->name('home'); // Trang chủ [cite: 29]
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->hasRole('host')) {
        return redirect()->route('host.dashboard');
    }

    return redirect()->route('home');
})->middleware('auth')->name('dashboard');
Route::get('/tours', [TourController::class, 'index'])->name('tours.index'); // Xem tất cả tour & Tìm kiếm
Route::get('/magazine', [PostController::class, 'index'])->name('magazine.index'); // Điểm đến & Bài viết [cite: 31]
Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.show'); // Chi tiết Tour [cite: 33]
Route::get('/magazine/{slug}', [PostController::class, 'show'])->name('magazine.show');
Route::get('/destinations/{slug}', [DestinationController::class, 'show'])->name('destinations.show'); // Trang điểm đến
Route::post('/ai/chat', AIChatController::class)->middleware('throttle:8,1')->name('ai.chat');
// Các tính năng yêu cầu đăng nhập và có role là 'tourist'
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/{slug}', [BookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/checkout/{tour_id}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/success/{id}', [BookingController::class, 'success'])->name('booking.success');
    
    // VNPAY
    Route::get('/vnpay/payment/{booking_id}', [PaymentController::class, 'createPayment'])->name('vnpay.payment');
    Route::post('/tours/{tour}/reviews', [ReviewController::class, 'store'])->name('tours.reviews.store');
    Route::get('/messages/{contact}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{receiver}', [MessageController::class, 'store'])->name('messages.store');
});

Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');

/*
|--------------------------------------------------------------------------
| Phân hệ 2: Chủ cơ sở (Host Dashboard)
|--------------------------------------------------------------------------
*/
// Yêu cầu đăng nhập và có role 'host'
Route::middleware(['auth', 'role:host'])->prefix('host')->name('host.')->group(function () {
    Route::get('/dashboard', [HostController::class, 'dashboard'])->name('dashboard'); // Tổng quan [cite: 39]
    Route::resource('tours', HostTourController::class)->except(['show']); // Quản lý Tour [cite: 40]
    Route::get('/bookings', [HostBookingController::class, 'index'])->name('bookings'); // Quản lý Khách hàng [cite: 41]
    Route::post('/bookings/{booking}/confirm', [HostBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/complete', [HostBookingController::class, 'complete'])->name('bookings.complete');
    Route::post('/bookings/{booking}/cancel', [HostBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/inbox', [MessageController::class, 'index'])->name('inbox'); // Nhắn tin [cite: 42]
});

/*
|--------------------------------------------------------------------------
| Phân hệ 3: Quản trị viên (Admin Dashboard)
|--------------------------------------------------------------------------
*/
// Yêu cầu đăng nhập và có role 'admin'
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('posts', AdminPostController::class)->except(['show']); // Quản lý Nội dung (CMS) [cite: 43]
    Route::get('/approvals', [AdminApprovalController::class, 'index'])->name('approvals'); // Kiểm duyệt Tour [cite: 44]
    Route::post('/approvals/{tour}/approve', [AdminApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{tour}/reject', [AdminApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions'); // Quản lý Giao dịch [cite: 44]
    Route::post('/transactions/{booking}/mark-paid', [AdminTransactionController::class, 'markPaid'])->name('transactions.mark-paid');
    Route::post('/transactions/{booking}/cancel', [AdminTransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::post('/users/{user}/approve-host', [AdminUserController::class, 'approveHost'])->name('users.approve-host');
    Route::post('/users/{user}/reject-host', [AdminUserController::class, 'rejectHost'])->name('users.reject-host');
    Route::post('/users/{user}/lock', [AdminUserController::class, 'lock'])->name('users.lock');
    Route::post('/users/{user}/unlock', [AdminUserController::class, 'unlock'])->name('users.unlock');
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::resource('coupons', CouponController::class)->except(['show']);
    Route::get('/refunds', [RefundController::class, 'index'])->name('refunds');
    Route::post('/refunds/{refund}/process', [RefundController::class, 'process'])->name('refunds.process');
});

Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'overview'])->name('overview');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::get('/favorites', [ProfileController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/{tour}', [ProfileController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/coupons', [ProfileController::class, 'coupons'])->name('coupons');
    Route::get('/security', [ProfileController::class, 'security'])->name('security');
    Route::post('/bookings/{booking}/refund', [ProfileController::class, 'requestRefund'])->name('refunds.store');
});

// Load routes của Laravel Breeze (Xử lý Login/Logout)
require __DIR__.'/auth.php';
