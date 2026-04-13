<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'index']);

Route::get('/start/{players}', [GameController::class, 'start']);

Route::get('/game', [GameController::class, 'game']);

Route::post('/play', [GameController::class, 'play']);

Route::post('/pickup', [GameController::class, 'pickup']);

