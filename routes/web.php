<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\CoffeeShopController;
use App\Http\Controllers\Admin\KomunitasController;
use App\Http\Controllers\Admin\KecamatanController;
use App\Http\Controllers\Admin\CoffeeShopReviewController;
use App\Http\Controllers\Admin\CommunityMemberController;
use App\Http\Controllers\Admin\GatheringRequestController;
use App\Http\Controllers\Admin\CommunityPostController;
use App\Http\Controllers\Admin\CommunityCommentController;

/*
|--------------------------------------------------------------------------
| WEB MIDDLEWARE - All routes should be in this group
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->group(function () {

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| PASSWORD RESET
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');


/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');

/*
|--------------------------------------------------------------------------
| COFFEE SHOPS
|--------------------------------------------------------------------------
*/

Route::get('/coffee-shops', [CoffeeShopController::class, 'index'])->name('coffee.index');
Route::get('/coffee-shops/create', [CoffeeShopController::class, 'create'])->name('coffee.create');
Route::post('/coffee-shops', [CoffeeShopController::class, 'store'])->name('coffee.store');
Route::get('/coffee-shops/{id}/edit', [CoffeeShopController::class, 'edit'])->name('coffee.edit');
Route::put('/coffee-shops/{id}', [CoffeeShopController::class, 'update'])->name('coffee.update');
Route::delete('/coffee-shops/{id}', [CoffeeShopController::class, 'destroy'])->name('coffee.destroy');

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::get('/login-monitor', [CoffeeShopController::class, 'loginMonitor'])->name('admin.login.monitor');
Route::get('/user/create', [CoffeeShopController::class, 'createUser'])->name('admin.user.create');
Route::post('/user', [CoffeeShopController::class, 'storeUser'])->name('admin.user.store');
Route::get('/user/{id}/edit', [CoffeeShopController::class, 'editUser'])->name('admin.user.edit');
Route::put('/user/{id}', [CoffeeShopController::class, 'updateUser'])->name('admin.user.update');
Route::delete('/user/{id}', [CoffeeShopController::class, 'destroyUser'])->name('admin.user.destroy');

/*
|--------------------------------------------------------------------------
| KOMUNITAS
|--------------------------------------------------------------------------
*/

Route::get('/komunitas', [KomunitasController::class, 'index'])->name('komunitas.index');
Route::get('/komunitas/create', [KomunitasController::class, 'create'])->name('komunitas.create');
Route::post('/komunitas', [KomunitasController::class, 'store'])->name('komunitas.store');
Route::get('/komunitas/{id}', [KomunitasController::class, 'show'])->name('komunitas.show');
Route::get('/komunitas/{id}/edit', [KomunitasController::class, 'edit'])->name('komunitas.edit');
Route::put('/komunitas/{id}', [KomunitasController::class, 'update'])->name('komunitas.update');
Route::delete('/komunitas/{id}', [KomunitasController::class, 'destroy'])->name('komunitas.destroy');

/*
|--------------------------------------------------------------------------
| KECAMATAN
|--------------------------------------------------------------------------
*/

Route::get('/kecamatan', [KecamatanController::class, 'index'])->name('kecamatan.index');
Route::get('/kecamatan/create', [KecamatanController::class, 'create'])->name('kecamatan.create');
Route::post('/kecamatan', [KecamatanController::class, 'store'])->name('kecamatan.store');
Route::get('/kecamatan/{id}/edit', [KecamatanController::class, 'edit'])->name('kecamatan.edit');
Route::put('/kecamatan/{id}', [KecamatanController::class, 'update'])->name('kecamatan.update');
Route::delete('/kecamatan/{id}', [KecamatanController::class, 'destroy'])->name('kecamatan.destroy');

/*
|--------------------------------------------------------------------------
| COFFEE SHOP REVIEWS
|--------------------------------------------------------------------------
*/

Route::get('/coffee-shop-reviews', [CoffeeShopReviewController::class, 'index'])->name('coffee-shop-reviews.index');
Route::get('/coffee-shop-reviews/create', [CoffeeShopReviewController::class, 'create'])->name('coffee-shop-reviews.create');
Route::post('/coffee-shop-reviews', [CoffeeShopReviewController::class, 'store'])->name('coffee-shop-reviews.store');
Route::get('/coffee-shop-reviews/{id}/edit', [CoffeeShopReviewController::class, 'edit'])->name('coffee-shop-reviews.edit');
Route::put('/coffee-shop-reviews/{id}', [CoffeeShopReviewController::class, 'update'])->name('coffee-shop-reviews.update');
Route::delete('/coffee-shop-reviews/{id}', [CoffeeShopReviewController::class, 'destroy'])->name('coffee-shop-reviews.destroy');

/*
|--------------------------------------------------------------------------
| COMMUNITY MEMBERS
|--------------------------------------------------------------------------
*/

Route::get('/community-members', [CommunityMemberController::class, 'index'])->name('community-members.index');
Route::get('/community-members/create', [CommunityMemberController::class, 'create'])->name('community-members.create');
Route::post('/community-members', [CommunityMemberController::class, 'store'])->name('community-members.store');
Route::get('/community-members/{id}/edit', [CommunityMemberController::class, 'edit'])->name('community-members.edit');
Route::put('/community-members/{id}', [CommunityMemberController::class, 'update'])->name('community-members.update');
Route::delete('/community-members/{id}', [CommunityMemberController::class, 'destroy'])->name('community-members.destroy');

/*
|--------------------------------------------------------------------------
| GATHERING REQUESTS
|--------------------------------------------------------------------------
*/

Route::get('/gathering-requests', [GatheringRequestController::class, 'index'])->name('gathering-requests.index');
Route::get('/gathering-requests/create', [GatheringRequestController::class, 'create'])->name('gathering-requests.create');
Route::post('/gathering-requests', [GatheringRequestController::class, 'store'])->name('gathering-requests.store');
Route::get('/gathering-requests/{id}/edit', [GatheringRequestController::class, 'edit'])->name('gathering-requests.edit');
Route::put('/gathering-requests/{id}', [GatheringRequestController::class, 'update'])->name('gathering-requests.update');
Route::post('/gathering-requests/{id}/status', [GatheringRequestController::class, 'updateStatus'])->name('gathering-requests.update-status');
Route::delete('/gathering-requests/{id}', [GatheringRequestController::class, 'destroy'])->name('gathering-requests.destroy');

/*
|--------------------------------------------------------------------------
| COMMUNITY POSTS
|--------------------------------------------------------------------------
*/

Route::get('/community-posts', [CommunityPostController::class, 'index'])->name('community-posts.index');
Route::get('/community-posts/create', [CommunityPostController::class, 'create'])->name('community-posts.create');
Route::post('/community-posts', [CommunityPostController::class, 'store'])->name('community-posts.store');
Route::get('/community-posts/{id}/edit', [CommunityPostController::class, 'edit'])->name('community-posts.edit');
Route::put('/community-posts/{id}', [CommunityPostController::class, 'update'])->name('community-posts.update');
Route::delete('/community-posts/{id}', [CommunityPostController::class, 'destroy'])->name('community-posts.destroy');

/*
|--------------------------------------------------------------------------
| COMMUNITY COMMENTS
|--------------------------------------------------------------------------
*/

Route::get('/community-comments', [CommunityCommentController::class, 'index'])->name('community-comments.index');
Route::get('/community-comments/{id}/edit', [CommunityCommentController::class, 'edit'])->name('community-comments.edit');
Route::put('/community-comments/{id}', [CommunityCommentController::class, 'update'])->name('community-comments.update');
Route::delete('/community-comments/{id}', [CommunityCommentController::class, 'destroy'])->name('community-comments.destroy');

});


/*
|--------------------------------------------------------------------------
| USER AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'userDashboard'])
        ->name('user.dashboard');

    Route::get('/my-profile', [AuthController::class, 'viewProfile'])
        ->name('user.view-profile');

    Route::get('/profile', [AuthController::class, 'editProfile'])
        ->name('user.profile');

    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('user.profile.update');
});

}); // End web middleware group
