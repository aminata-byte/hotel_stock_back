<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ✅ Route pour obtenir l'utilisateur authentifié
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Routes publiques (sans authentification)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/hotels-public', [HotelController::class, 'index']); // liste des hôtels accessible à tous
Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne parfaitement !',
        'status' => 'success',
        'timestamp' => now()
    ]);
});

// ✅ Routes protégées (nécessitent une authentification Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('hotels', HotelController::class);
    Route::get('/my-hotels', [HotelController::class, 'myHotels']);
});
