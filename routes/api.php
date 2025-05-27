<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

Route::post('/login',[AuthController::class,'register']);
Route::post('/register',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout']);


Route::apiResource('note', NoteController::class)->middleware('auth:sanctum');
