<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'index']);
Route::get('/start/{players}', [GameController::class, 'start']);
Route::get('/game', [GameController::class, 'game']);
Route::post('/play', [GameController::class, 'play']);
Route::post('/pickup', [GameController::class, 'pickup']);

// Auth
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister']);
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

// Highscore
Route::get('/highscores', [\App\Http\Controllers\ScoreController::class, 'index']);

