<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameTypeController;
use App\Http\Controllers\Api\MatchController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::get('/game-types', [GameTypeController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/matches', [MatchController::class, 'store']);
    Route::patch('/matches/{match}/finish', [MatchController::class, 'finish']);
});
