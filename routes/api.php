<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

// NOTE ROUTES
Route::apiResource('note', NoteController::class)
    ->middleware('auth:sanctum')
    ->middleware('can:modify,note')->only(['show', 'update', 'destroy']);

Route::get('/note', [NoteController::class, 'index'])
    ->middleware('auth:sanctum');
    
Route::post('/note', [NoteController::class, 'store'])
    ->middleware('auth:sanctum');

