<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerOrderController;

Route::get('/', fn () => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->isPenjual()
            ? redirect()->route('penjual.dashboard')
            : redirect()->route('pembeli.dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Pembeli
    Route::get('/pembeli/dashboard', fn () => view('pembeli.dashboard'))
        ->middleware('role:pembeli')->name('pembeli.dashboard');

    // Penjual
    Route::middleware('role:penjual')->prefix('penjual')->name('penjual.')->group(function () {
        Route::get('/dashboard', fn () => view('penjual.dashboard'))->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show', 'create']);

        Route::resource('products', ProductController::class)->except(['show']);
        Route::patch('products/{product}/status', [ProductController::class, 'toggleStatus'])
            ->name('products.status');

        Route::get('orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [SellerOrderController::class, 'updateStatus'])
            ->name('orders.status');
    });

        // Pembeli
    Route::middleware('role:pembeli')->prefix('pembeli')->name('pembeli.')->group(function () {
        Route::get('/dashboard', fn () => view('pembeli.dashboard'))->name('dashboard');
 
        Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/katalog/{product}', [CatalogController::class, 'show'])->name('catalog.show');
 
        Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
        Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/keranjang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
        
        Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
 
        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order}/bukti', [OrderController::class, 'uploadProof'])->name('orders.proof');
    });
});