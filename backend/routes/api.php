<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhotoController;

Route::middleware('api')->group(function () {
    // Listings
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);

    // Photo upload for listings (auth required)
    Route::post('/listings/{id}/photos', [PhotoController::class, 'upload'])->middleware('auth:sanctum');

    // Auth / SMS verification
    Route::post('/auth/send-code', [AuthController::class, 'sendCode']);
    Route::post('/auth/verify-code', [AuthController::class, 'verifyCode']);
    Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout']);

    // Auth placeholder (legacy)
    Route::post('/auth/login', function(Request $r){ return response()->json(['message'=>'implement login']); });
});
