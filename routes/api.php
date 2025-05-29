<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// NOTE ROUTES

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('note', NoteController::class)
        ->middleware('can:modify,note')->only(['show', 'update', 'destroy']);

    Route::apiResource('note', NoteController::class)->only(['index', 'show', 'store']);
});


