<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CoffeeShopController;

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
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
Route::get('/coffee-shops', [CoffeeShopController::class, 'index'])->name('coffee.index');
Route::get('/coffee-shops/create', [CoffeeShopController::class, 'create'])->name('coffee.create');
Route::post('/coffee-shops', [CoffeeShopController::class, 'store'])->name('coffee.store');
Route::get('/coffee-shops/{id}/edit', [CoffeeShopController::class, 'edit'])->name('coffee.edit');
Route::put('/coffee-shops/{id}', [CoffeeShopController::class, 'update'])->name('coffee.update');
Route::delete('/coffee-shops/{id}', [CoffeeShopController::class, 'destroy'])->name('coffee.destroy');
Route::get('/login-monitor', [CoffeeShopController::class, 'loginMonitor'])->name('admin.login.monitor');
Route::delete('/login-monitor/{id}', [CoffeeShopController::class, 'deleteLog'])->name('admin.login.monitor.delete');

});


/*
|--------------------------------------------------------------------------
| USER AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {

    Route::get('/dashboard', [AuthController::class, 'userDashboard'])
        ->name('user.dashboard');

    Route::get('/profile', [AuthController::class, 'editProfile'])
        ->name('user.profile');

    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('user.profile.update');
});
