<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\OrderController; 
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportController; 
use App\Http\Controllers\KatalogController; 
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\Admin\CategoryController; 
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Admin\ThemeController;

Route::get('/', [KatalogController::class, 'index']);

Route::get('/kategori', [KatalogController::class, 'showCategoryGrid'])
    ->name('kategori.index');
Route::get('/kategori/{kategori_slug}', [KatalogController::class, 'showProductsByCategory'])
    ->name('produk.by.kategori');
Route::get('/cari', [KatalogController::class, 'search'])
    ->name('produk.cari');
Route::get('/produk/{product}', function (App\Models\Product $product) {
    return view('produk.detail', compact('product'));
})->name('produk.detail');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/verify-otp/resend', [App\Http\Controllers\Auth\OtpController::class, 'resend'])->name('otp.resend');
Route::post('/ajax/send-otp', [OtpAjaxController::class, 'send'])->name('ajax.send.otp');

Route::middleware(['auth'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/checkout', [KeranjangController::class, 'checkout'])->name('checkout.process');
    Route::get('/riwayat-pesanan', [UserOrderController::class, 'index'])->name('riwayat.index');
    Route::get('/profil-saya', [UserController::class, 'show'])->name('user.profil');
    Route::put('/profil-saya', [UserController::class, 'update'])->name('user.profil.update');   
    Route::get('/user/notifications', [UserNotificationController::class, 'index'])->name('user.notifications.index');
    Route::get('/user/notifications/{id}/read', [UserNotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/user/notifications/read-all', [UserNotificationController::class, 'markAllRead'])->name('user.notifications.readAll'); 
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [ProductController::class, 'index'])->name('dashboard');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::resource('categories', CategoryController::class);
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show'); 
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-transactions', [ReportController::class, 'exportTransactions'])->name('reports.export.transactions');
    Route::get('/reports/export-products', [ReportController::class, 'exportProducts'])->name('reports.export.products');
    Route::resource('themes', ThemeController::class);
    Route::post('/themes/{theme}/activate', [ThemeController::class, 'activate'])->name('themes.activate');
    Route::post('/themes/{theme}/deactivate', [ThemeController::class, 'deactivate'])->name('themes.deactivate');
});