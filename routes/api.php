<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\GameType\FormatsIndexController;
use App\Http\Controllers\Api\GameType\IndexController as GameTypeIndexController;
use App\Http\Controllers\Api\Match\FinishController;
use App\Http\Controllers\Api\Match\StoreController;
use App\Http\Controllers\Api\Player\IndexController as PlayerIndexController;
use App\Http\Controllers\Api\User\CurrentUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', LoginController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', CurrentUserController::class);
        Route::post('/logout', LogoutController::class);
    });
});

Route::get('/game-types', GameTypeIndexController::class);
Route::get('/game-types/{gameType}/formats', FormatsIndexController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/players', PlayerIndexController::class);
    Route::post('/matches', StoreController::class);
    Route::patch('/matches/{match}/finish', FinishController::class);
});
