<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CoffeeShopController;
use App\Http\Controllers\Api\KomunitasController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute API untuk Testing Ujian (Postman)
Route::get('/kedai', function () {
    return response()->json([
        'status' => 'success',
        'data' => \App\Models\CoffeeShop::with('kecamatan')->get()
    ]);
});

Route::get('/komunitas', function () {
    return response()->json([
        'status' => 'success',
        'data' => \App\Models\Komunitas::with(['members.user', 'posts'])->get()
    ]);
});

// Original Routes (kept for compatibility if needed)
Route::name('api.')->group(function () {
    Route::apiResource('coffee-shops', CoffeeShopController::class);
    Route::apiResource('komunitas-resource', KomunitasController::class);
});
 