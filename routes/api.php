<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoffeeShopController;
use App\Http\Controllers\Api\KomunitasController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute API
Route::name('api.')->group(function () {
    Route::apiResource('coffee-shops', CoffeeShopController::class);
    Route::apiResource('komunitas', KomunitasController::class);
});
 