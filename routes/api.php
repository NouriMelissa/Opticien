<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\LigneVenteController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\WilayaController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\SessionShiftController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrdonnanceController;


/*
|--------------------------------------------------------------------------
| Ventes
|--------------------------------------------------------------------------
*/
Route::apiResource('ventes', VenteController::class);
Route::post('ventes/{vente}/valider', [VenteController::class, 'valider']);
Route::post('ventes/{vente}/annuler', [VenteController::class, 'annuler']);

/*
|--------------------------------------------------------------------------
| Lignes Vente
|--------------------------------------------------------------------------
*/
// Lire toutes les lignes d'une vente
Route::get('ventes/{idVente}/lignes', [LigneVenteController::class, 'index']);

// CRUD sur une ligne (hors index)
Route::apiResource('lignes-vente', LigneVenteController::class)->except(['index']);

/*
|--------------------------------------------------------------------------
| Commandes Fournisseur
|--------------------------------------------------------------------------
*/
Route::apiResource('commandes', CommandeController::class);
Route::post('commandes/{commande}/receptionner', [CommandeController::class, 'receptionner']);
Route::post('commandes/{commande}/annuler',      [CommandeController::class, 'annuler']);
// ════════════════════════════════════════════════════════════════
// TEST — Vérifier que l'API fonctionne (garder tel quel)
// GET /api/hello
// ════════════════════════════════════════════════════════════════

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


// ── CLIENTS ──────────────────────────────────────────────────
    Route::get('clients/dropdown', [ClientController::class, 'dropdown']);
    Route::apiResource('clients', ClientController::class);
 
    // Sous-ressources de Client (historique ordonnances)
    Route::get('clients/{idClient}/ordonnances', [OrdonnanceController::class, 'historiqueClient']);
    Route::get('clients/{idClient}/ordonnances/derniere', [OrdonnanceController::class, 'derniereDuClient']);
 
    // ── ORDONNANCES ──────────────────────────────────────────────
    Route::apiResource('ordonnances', OrdonnanceController::class);
    });