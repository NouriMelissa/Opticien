<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\WilayaController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\ClientController;

Route::get('/hello', function () {
    return response()->json(['message' => 'API is working', 'project' => 'OptiKlear']);
});

Route::prefix('v1')->group(function () {

    // ── WILAYAS ──────────────────────────────────────────────────
    Route::get('wilayas/dropdown', [WilayaController::class, 'dropdown']);
    Route::apiResource('wilayas', WilayaController::class);

    // ── COMMUNES ─────────────────────────────────────────────────
    Route::get('communes/dropdown', [CommuneController::class, 'dropdown']);
    Route::apiResource('communes', CommuneController::class);

    // ── MEDECINS ─────────────────────────────────────────────────
    Route::get('medecins/dropdown', [MedecinController::class, 'dropdown']);
    Route::apiResource('medecins', MedecinController::class);

    // ── CLIENTS ──────────────────────────────────────────────────
    Route::get('clients/dropdown', [ClientController::class, 'dropdown']);
    Route::apiResource('clients', ClientController::class);
});