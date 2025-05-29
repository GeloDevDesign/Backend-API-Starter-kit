<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// NOTE ROUTES

Route::middleware(['auth:sanctum', 'throttle:global'])->prefix('note')->group(function () {
    
    Route::apiResource('/', NoteController::class)
        ->middleware('can:modify,note')
        ->only(['show', 'update', 'destroy']);
        
    Route::apiResource('/', NoteController::class)
        ->only(['index', 'store']);
});


