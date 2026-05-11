<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoffeeShopController;
use App\Http\Controllers\Api\KomunitasController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute API Coffee Shop
Route::apiResource('coffee-shops', CoffeeShopController::class);

// Rute API Komunitas
Route::apiResource('komunitas', KomunitasController::class);
 