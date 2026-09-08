<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectListController;
use App\Http\Controllers\TaskController;


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




    Route::post('/{project}/lists', [ProjectListController::class, 'create'])->name('projects.lists.create')->middleware('auth:sanctum');
    Route::post('/{project}/lists/{list}', [ProjectListController::class, 'update'])->name('projects.lists.update')->middleware('auth:sanctum');
    Route::delete('/{project}/lists/{list}', [ProjectListController::class, 'destroy'])->name('projects.lists.destroy')->middleware('auth:sanctum');   
});

// Tasks (Shallow RESTful routes)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/lists/{list}/tasks', [TaskController::class, 'create'])->name('lists.tasks.create');
    Route::match(['post', 'put', 'patch'], '/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

