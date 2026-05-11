<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoffeeShopController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Tambahkan rute API Coffee Shop Anda di sini
Route::apiResource('coffee-shops', CoffeeShopController::class);
 