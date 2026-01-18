<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Productos
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/stock/check', [StockController::class, 'check'])->name('stock.check');

// Carrito
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show')->middleware('web');
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::delete('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/calculate-fees', [CheckoutController::class, 'calculateFees'])->name('checkout.calculateFees');
// Payment verification routes removed - payments handled via Odoo invoices

// Órdenes que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('order');
});

// Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Páginas estáticas
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/fees-surcharges', [PageController::class, 'fees'])->name('fees');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/site-map', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/terms-conditions', [PageController::class, 'terms'])->name('terms');
Route::get('/sms-policy', [PageController::class, 'sms'])->name('sms');
Route::get('/directions', [PageController::class, 'directions'])->name('directions');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [PageController::class, 'showPost'])->name('blog.post');
