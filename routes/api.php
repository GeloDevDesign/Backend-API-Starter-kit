<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');


Route::apiResource('note', NoteController::class);
