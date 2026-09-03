<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the Task Management API']);
});


// Public auth routes — throttled to 10 requests/minute to prevent brute-force
Route::prefix('auth')->middleware(['throttle:10,1'])->group(function () {
    Route::post('register', [AuthController::class, 'registration'])->name('register');
    Route::post('verify',   [AuthController::class, 'verify'])->name('verify');
    Route::post('login',    [AuthController::class, 'login'])->name('login');
});






// Protected routes — require a valid Sanctum token
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
});
