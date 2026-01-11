<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return redirect()->route('menu');
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/cart', [MenuController::class, 'cart'])->name('cart');
Route::post('/cart/add', [MenuController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [MenuController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [MenuController::class, 'removeItemFromCart'])->name('cart.remove');
Route::get('/cart/clear', [MenuController::class, 'clearCart'])->name('cart.clear');

Route::get('/checkout', [MenuController::class, 'checkout'])->name('checkout');
Route::post('/checkout/store', [MenuController::class, 'storeOrder'])->name('checkout.store');
Route::get('/checkout/success/{orderId}', [MenuController::class, 'checkoutSuccess'])->name('checkout.success');

// Admin Routes
Route::middleware('role:admin')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('items', ItemController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
});

Route::middleware('role:admin|cashier|chef')->group(function () {
    Route::resource('orders', OrderController::class);
    Route::post('items/update-status/{id}', [ItemController::class, 'updateStatus'])->name('items.updateStatus');
    Route::post('orders/{id}', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});
