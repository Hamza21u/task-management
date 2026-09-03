<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;


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
    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::prefix('projects')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [ProjectController::class, 'list'])->name('projects.list')->middleware('auth:sanctum');
    Route::post('create', [ProjectController::class, 'create'])->name('projects.create')->middleware('auth:sanctum');
    Route::post('/update', [ProjectController::class, 'update'])->name('projects.update')->middleware('auth:sanctum');
    Route::delete('/destroy', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('auth:sanctum');
});