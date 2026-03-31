<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\TableTokenController;
use App\Http\Controllers\PaymentCallbackController;

Route::get('/', function () {
    return redirect()->route('menu');
});

Route::post('/midtrans/callback', [PaymentCallbackController::class, 'receive']);

Route::get('/scan/{meja}', [MenuController::class, 'scanTable'])->name('scan.table');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/cart', [MenuController::class, 'cart'])->name('cart');
Route::post('/cart/add', [MenuController::class, 'addToCart'])->middleware('throttle:30,1')->name('cart.add');
Route::post('/cart/update', [MenuController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [MenuController::class, 'removeItemFromCart'])->name('cart.remove');
Route::get('/cart/clear', [MenuController::class, 'clearCart'])->name('cart.clear');

Route::get('/checkout', [MenuController::class, 'checkout'])->name('checkout');
Route::post('/checkout/store', [MenuController::class, 'storeOrder'])->middleware('throttle:3,1')->name('checkout.store');
Route::get('/checkout/success/{orderId}', [MenuController::class, 'checkoutSuccess'])->name('checkout.success');

// Admin Routes
Route::middleware('role:admin')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('items', ItemController::class)->except(['index']);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
});

Route::middleware('role:admin|cashier|chef')->group(function () {
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::resource('orders', OrderController::class);
    Route::post('items/update-status/{id}', [ItemController::class, 'updateStatus'])->name('items.updateStatus');
    Route::post('orders/{id}', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('role:admin|cashier')->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::post('/pos/update-status/{orderCode}', [PosController::class, 'updateStatus'])->name('pos.updateStatus');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Table Token Management
    Route::get('/table-tokens', [TableTokenController::class, 'index'])->name('table-tokens.index');
    Route::post('/table-tokens', [TableTokenController::class, 'generate'])->name('table-tokens.generate');
    Route::post('/table-tokens/{id}/revoke', [TableTokenController::class, 'revoke'])->name('table-tokens.revoke');
    Route::delete('/table-tokens/{id}', [TableTokenController::class, 'destroy'])->name('table-tokens.destroy');
    Route::post('/table-tokens/cleanup', [TableTokenController::class, 'cleanup'])->name('table-tokens.cleanup');
});
