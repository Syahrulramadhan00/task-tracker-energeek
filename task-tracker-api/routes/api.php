<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

// Auth Routes (Tidak ada halaman register)
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Harus login & pakai token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::apiResource('projects', ProjectController::class)->except(['destroy']);
    
    Route::apiResource('tasks', TaskController::class);
});
