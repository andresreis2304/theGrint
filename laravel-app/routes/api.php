<?php

use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']); // public
Route::post('/login',    [AuthController::class, 'login']);    // public
Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/ads', [AnuncioController::class, 'index']); // public

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ads', [AnuncioController::class, 'store']);
    Route::delete('/ads/{id}', [AnuncioController::class, 'destroy']);
});