<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('isEmailVerified')
    ->name('dashboard');

Route::middleware(['authenticate', 'isEmailVerified', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)
        ->withoutMiddlewareFor(['create', 'store'], ['authenticate', 'isEmailVerified', 'role:admin']);
    Route::post('/users/add-credits/{user}', [UserController::class, 'addCredits'])->name('users.add-credits');
});

Route::get('/', function () {
    return view('user.create');
})->middleware('authenticate');

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');
Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');


Route::get('/email/verify' , [EmailVerificationController::class, 'showEmailVerificationPage'])
    ->name('verification.notice');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationMail'])
    ->name('verification.send');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])
    ->name('verification.verify');


Route::get('/forgot-password', [ResetPasswordController::class, 'resetPasswordEmail'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/forgot-password' , [ResetPasswordController::class, 'resetPassword'])
    ->name('password.email');
Route::get('/reset-password/email/{token}', [ResetPasswordController::class, 'resetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'resetUserPassword'])
    ->middleware('guest')
    ->name('password.update');

Route::resource('products', ProductController::class)->middleware(['role:admin']);

Route::post('products/get-tags', [ProductController::class, 'getTags'])->name('products.getTags');
Route::post('products/{id}/get-tags', [ProductController::class, 'getTags'])->name('products.getTags.withId');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::post('/add-to-cart', [MenuController::class, 'addToCart'])->name('menu.add-to-cart');
Route::post('/render-cart-summary', [MenuController::class, 'renderCartSummary'])->name('menu.render-cart-summary');
Route::post('/already-in-cart', [MenuController::class, 'alreadyInCart'])->name('menu.already-in-cart');
Route::post('/update-cart', [MenuController::class, 'updateCart'])->name('update-cart');
Route::post('/remove-from-cart', [MenuController::class, 'removeFromCart'])->name('menu.remove-from-cart');

Route::post('order/update-cart', [MenuController::class, 'updateCart'])->name('order.update-cart');
Route::post('order/remove-from-cart', [MenuController::class, 'removeFromCart'])->name('order.remove-from-cart');

//Route::get('/order', [OrderController::class, 'index'])->name('order.index');
//Route::post('order/store', [OrderController::class, 'store'])->name('order.store');
Route::resource('/order', OrderController::class);


Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

Route::post('/create-order', [CheckoutController::class, 'createOrder'])->name('create-order');


Route::resource('/giftcards', GiftCardController::class)->middleware(['role:admin']);

Route::get('/user-orders', [OrderController::class , 'displayUserOrders'])->name('user-orders.index');
Route::post('/validate-code', [OrderController::class, 'validateCode'])->name('validate-code');

Route::post('/remove-giftcard', [OrderController::class, 'removeGiftCard'])->name('remove-giftcard');


Route::resource('roles', RoleController::class)->middleware(['role:admin']);
Route::resource('permissions', PermissionController::class)->middleware(['role:admin']);

Route::post('/search-products', [ProductController::class, 'searchProducts'])->name('search-products');
Route::post('/search-product-variants', [ProductController::class, 'searchProductVariants'])->name('search-product-variants');
Route::post('/add-to-order', [OrderController::class, 'addToOrder'])->name('order.addToOrder');
