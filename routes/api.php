<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the Task Management API']);
});



Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'registration'])->name('register');
    Route::post('verify', [AuthController::class, 'verify'])->name('verify');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

