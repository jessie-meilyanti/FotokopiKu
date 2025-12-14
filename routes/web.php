<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/produk/{slug}', [CatalogController::class, 'show'])->name('produk.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('home'))->name('dashboard');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'form'])->name('checkout.form');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/proof', [OrderController::class, 'uploadProof'])->name('orders.proof');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
});

Route::middleware(['auth', 'can:is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'dashboard'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/feed', [AdminOrderController::class, 'feed'])->name('orders.feed');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/track', [AdminOrderController::class, 'addTrack'])->name('orders.track');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
