<?php

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnuncioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/ads', [AnuncioController::class, 'index']); // public


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('dev-auth')->group(function () {
    Route::post('/ads', [AnuncioController::class, 'store']);
    Route::delete('/ads/{id}', [AnuncioController::class, 'destroy']);
});


