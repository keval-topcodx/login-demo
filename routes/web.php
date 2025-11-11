<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
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

Route::resource('products', ProductController::class)->middleware(['role:admin'])->middleware('authenticate');

Route::post('products/get-tags', [ProductController::class, 'getTags'])->name('products.getTags');
Route::post('products/{id}/get-tags', [ProductController::class, 'getTags'])->name('products.getTags.withId');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index')->middleware('authenticate');
Route::post('/add-to-cart', [MenuController::class, 'addToCart'])->name('menu.add-to-cart');
Route::post('/render-cart-summary', [MenuController::class, 'renderCartSummary'])->name('menu.render-cart-summary');
Route::post('/already-in-cart', [MenuController::class, 'alreadyInCart'])->name('menu.already-in-cart');
Route::post('/update-cart', [MenuController::class, 'updateCart'])->name('update-cart');
Route::post('/remove-from-cart', [MenuController::class, 'removeFromCart'])->name('menu.remove-from-cart');

Route::post('order/update-cart', [MenuController::class, 'updateCart'])->name('order.update-cart');
Route::post('order/remove-from-cart', [MenuController::class, 'removeFromCart'])->name('order.remove-from-cart');

Route::resource('/order', OrderController::class)->except(['update'])->middleware('authenticate');
Route::put('/order/{order}', [OrderController::class, 'update'])->name('order.update');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

Route::post('/create-order', [CheckoutController::class, 'createOrder'])->name('create-order');


Route::resource('/giftcards', GiftCardController::class)->middleware(['role:admin', 'authenticate']);

Route::get('/user-orders', [OrderController::class , 'displayUserOrders'])->name('user-orders.index');
Route::post('/validate-code', [OrderController::class, 'validateCode'])->name('validate-code');

Route::post('/remove-giftcard', [OrderController::class, 'removeGiftCard'])->name('remove-giftcard');


Route::resource('roles', RoleController::class)->middleware(['role:admin'])->middleware('authenticate');
Route::resource('permissions', PermissionController::class)->middleware(['role:admin'])->middleware('authenticate');

Route::post('/search-products', [ProductController::class, 'searchProducts'])->name('search-products');
Route::post('/search-product-variants', [ProductController::class, 'searchProductVariants'])->name('search-product-variants');
Route::post('/add-to-order', [OrderController::class, 'addToOrder'])->name('order.addToOrder');

Route::post('/suggest-products', [ProductController::class, 'suggestProducts'])->name('suggest-products');
Route::post('/suggest-variants', [ProductController::class, 'suggestVariants'])->name('suggest-variants');

Route::resource('chat', ChatController::class)->middleware(['role:admin|agent']);
Route::post('/search-user', [UserController::class, 'searchUser'])->name('search-user');

Route::post('/start-new-chat', [ChatController::class, 'startNewChat'])->name('start-new-chat');
Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send-message');
Route::post('/load-messages', [ChatController::class, 'loadMessages'])->name('load-messages');
Route::post('/load-chat', [ChatController::class, 'loadChat'])->name('load-chat');
Route::post('/send-user-message', [ChatController::class, 'sendUserMessage'])->name('send-user-message');
Route::post('/delete-message', [ChatController::class, 'deleteMessage'])->name('delete-message');
Route::post('/edit-message', [ChatController::class, 'editMessage'])->name('edit-message');
Route::post('/archive-chat', [ChatController::class, 'archiveChat'])->name('archive-chat');
Route::post('/unarchive-chat', [ChatController::class, 'unArchiveChat'])->name('unarchive-chat');
Route::post('/load-archived-chats', [ChatController::class, 'loadArchivedChats'])->name('load-archived-chats');
Route::post('/load-active-chats', [ChatController::class, 'loadActiveChats'])->name('load-active-chats');
Route::post('/chat-search', [ChatController::class, 'chatSearch'])->name('chat-search');
Route::post('/mark-as-read', [ChatController::class, 'markAsRead'])->name('mark-as-read');

Route::post('/get-all-notifications', [NotificationController::class, 'getAllNotifications'])->name('get-all-notifications');
Route::post('/mark-notification-as-read', [NotificationController::class, 'markNotificationAsRead'])->name('mark-notification-as-read');
