<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedecinController;

// ════════════════════════════════════════════════════════════════
// TEST — Vérifier que l'API fonctionne (garder tel quel)
// GET /api/hello
// ════════════════════════════════════════════════════════════════
Route::get('/hello', function () {
    return response()->json([
        'message' => 'API is working',
        'version' => '1.0',
        'project' => 'OptiKlear',
    ]);
});

// ════════════════════════════════════════════════════════════════
// ROUTES PUBLIQUES — Pas besoin d'être connecté
// ════════════════════════════════════════════════════════════════
Route::prefix('auth')->group(function () {
    // Route::post('/login',  [AuthController::class, 'login']);
    // Route::post('/logout', [AuthController::class, 'logout']);
});

// ════════════════════════════════════════════════════════════════
// ROUTES PROTÉGÉES — Préfixe /api/v1/...
// Décommenter ->middleware('auth:sanctum') quand auth prête
// ════════════════════════════════════════════════════════════════
Route::prefix('v1')->group(function () {

    // ── MEDECINS ─────────────────────────────────────────────────
    // IMPORTANT : dropdown AVANT apiResource sinon conflit avec {id}
    Route::get('medecins/dropdown', [MedecinController::class, 'dropdown']);

    // 5 routes CRUD générées automatiquement :
    // GET    /api/v1/medecins        → index()
    // POST   /api/v1/medecins        → store()
    // GET    /api/v1/medecins/{id}   → show()
    // PUT    /api/v1/medecins/{id}   → update()
    // DELETE /api/v1/medecins/{id}   → destroy()
    Route::apiResource('medecins', MedecinController::class);

    // ── Futures routes ────────────────────────────────────────────
    // Route::apiResource('clients',      ClientController::class);
    // Route::apiResource('articles',     ArticleController::class);
    // Route::apiResource('ventes',       VenteController::class);
    // Route::apiResource('fournisseurs', FournisseurController::class);
    // Route::apiResource('wilayas',      WilayaController::class);
});