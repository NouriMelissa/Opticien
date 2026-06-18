<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\SessionShiftController;
use App\Http\Controllers\AuthController;

// UTILISATEURS CRUD
Route::get('/utilisateurs', [UtilisateurController::class, 'index']);
Route::get('/utilisateurs/{id}', [UtilisateurController::class, 'show']);
Route::post('/utilisateurs', [UtilisateurController::class, 'store']);
Route::put('/utilisateurs/{id}', [UtilisateurController::class, 'update']);
Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy']);

// SESSIONS (
Route::get('/sessions', [SessionShiftController::class, 'index']);
Route::post('/sessions', [SessionShiftController::class, 'store']);
Route::get('/sessions/{id}', [SessionShiftController::class, 'show']);
Route::put('/sessions/{id}/close', [SessionShiftController::class, 'close']);
Route::delete('/sessions/{id}', [SessionShiftController::class, 'destroy']);

//Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);