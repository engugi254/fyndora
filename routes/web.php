<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DarajaController;

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migrations executed!';
});


// 🏠 Shop routes
Route::get('/', [ProductController::class, 'index'])->name('shop.index');
Route::get('/cart', [ProductController::class, 'cart'])->name('shop.cart');
Route::post('/add-to-cart/{id}', [ProductController::class, 'addToCart'])->name('shop.addToCart');
Route::get('/remove-from-cart/{id}', [ProductController::class, 'removeFromCart'])->name('shop.removeFromCart');
Route::get('/checkout', [ProductController::class, 'checkout'])->name('shop.checkout');
Route::post('/update-cart/{id}', [ProductController::class, 'updateCart'])->name('shop.updateCart');


Auth::routes();

// 🧑‍💼 Admin routes 
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']); // dashboard
	Route::get('/admin/products', [AdminController::class, 'products']); // product management
	Route::get('/admin/customers', [AdminController::class, 'customers']);
	Route::get('/admin/sales', [AdminController::class, 'sales']);
    Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
});

Route::post('/initiate-stk', [DarajaController::class, 'initiateStk']);
Route::post('/mpesa/callback', [DarajaController::class, 'mpesaCallback']);
Route::get('/check-payment-status', [DarajaController::class, 'checkPaymentStatus']);

Route::get('/payment-success', function () {
    return view('payment-success');
});

Route::get('/payment-failed', function () {
    return view('payment-failed');
});
