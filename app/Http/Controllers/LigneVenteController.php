<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLigneVenteRequest;
use App\Http\Requests\UpdateLigneVenteRequest;
use App\Http\Resources\LigneVenteResource;
use App\Models\LigneVente;
use App\Services\LigneVenteService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LigneVenteController extends Controller
{
    public function __construct(private LigneVenteService $service) {}

    /**
     * GET /api/ventes/{idVente}/lignes
     */
    public function index(Request $request, int $idVente)
    {
        $lignes = $this->service->lister($idVente);

        return LigneVenteResource::collection($lignes);
    }

    /**
     * GET /api/lignes-vente/{ligneVente}
     */
    public function show(LigneVente $ligneVente)
    {
        $ligneVente->load('article', 'tarifVerre', 'typeVerre', 'vente');

        return new LigneVenteResource($ligneVente);
    }

    /**
     * POST /api/lignes-vente
     */
    public function store(StoreLigneVenteRequest $request)
    {
        try {
            $ligne = $this->service->ajouter($request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return new LigneVenteResource($ligne);
    }

    /**
     * PUT /api/lignes-vente/{ligneVente}
     */
    public function update(UpdateLigneVenteRequest $request, LigneVente $ligneVente)
    {
        try {
            $ligne = $this->service->modifier($ligneVente, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return new LigneVenteResource($ligne);
    }

    /**
     * DELETE /api/lignes-vente/{ligneVente}
     */
    public function destroy(LigneVente $ligneVente)
    {
        try {
            $this->service->supprimer($ligneVente);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return response()->noContent();
    }
}