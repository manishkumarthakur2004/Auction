<?php

use App\Http\Controllers\Admin\AuctionController as AdminAuctionController;
use App\Http\Controllers\Admin\AuctionRequestController as AdminAuctionRequestController;
use App\Http\Controllers\Admin\BidController as AdminBidController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyBidController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuctionRequestController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\WonAuctionController;

use Illuminate\Support\Facades\Route;



Route::get('/', HomeController::class)->name('home');
Route::resource('auctions', AuctionController::class)->only(['index', 'show']);

Route::redirect('register', '/user/register')->name('register');
Route::redirect('login', '/user/login')->name('login');

Route::middleware('guest:web')->group(function () {
    Route::get('user/register', [RegisteredUserController::class, 'create'])->name('user.register');
    Route::post('user/register', [RegisteredUserController::class, 'store'])->name('user.register.store');
    Route::get('user/login', [AuthenticatedSessionController::class, 'create'])->name('user.login');
    Route::post('user/login', [AuthenticatedSessionController::class, 'store'])->name('user.login.store');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('admin/login', [AuthenticatedSessionController::class, 'adminCreate'])->name('admin.login');
    Route::post('admin/login', [AuthenticatedSessionController::class, 'adminStore'])->name('admin.login.store');
});

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth:web')->group(function () {
    Route::post('user/logout', [AuthenticatedSessionController::class, 'destroy'])->name('user.logout');
    Route::get('user/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::redirect('dashboard', '/user/dashboard');
    Route::get('user/dashboard', DashboardController::class)->name('dashboard');
    Route::get('user/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('user/notifications/read', [NotificationController::class, 'markAllRead'])->name('notifications.read');
    Route::get('user/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::get('user/my-bids', MyBidController::class)->name('bids.mine');
    Route::get('user/won-auctions', WonAuctionController::class)->name('won-auctions');
    Route::get('user/wishlist', [FavoriteController::class, 'index'])->name('wishlist.index');
    Route::post('user/wishlist/{auction}/toggle', [FavoriteController::class, 'toggle'])->name('wishlist.toggle');
    Route::resource('sell-requests', AuctionRequestController::class)
        ->only(['index', 'create', 'store', 'edit', 'update'])
        ->parameters(['sell-requests' => 'auction_request']);
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('auctions/{auction}/bids', [BidController::class, 'store'])->name('auctions.bids.store');
});

Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'adminDestroy'])->name('logout');
    Route::get('logout', [AuthenticatedSessionController::class, 'adminDestroy']);
    Route::redirect('/', '/admin/dashboard');
    Route::get('dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('users', AdminUserController::class)->only(['index', 'update', 'destroy']);
    Route::post('auctions/{auction}/feature', [AdminAuctionController::class, 'feature'])->name('auctions.feature');
    Route::post('auctions/{auction}/close', [AdminAuctionController::class, 'close'])->name('auctions.close');
    Route::resource('auctions', AdminAuctionController::class)->except(['show']);
    Route::get('seller-requests', [AdminAuctionRequestController::class, 'index'])->name('seller-requests.index');
    Route::post('seller-requests/{auctionRequest}/approve', [AdminAuctionRequestController::class, 'approve'])->name('seller-requests.approve');
    Route::post('seller-requests/{auctionRequest}/reject', [AdminAuctionRequestController::class, 'reject'])->name('seller-requests.reject');
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'destroy']);
    Route::get('bids', [AdminBidController::class, 'index'])->name('bids.index');
    Route::get('reports', AdminReportController::class)->name('reports');
});
