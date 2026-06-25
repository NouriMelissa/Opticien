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

    public function index(Request $request, int $idVente)
    {
        try {
            $lignes = $this->service->lister($idVente);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Vente introuvable.', 'errors' => $e->errors()], 404);
        }

        return LigneVenteResource::collection($lignes);
    }

    public function show(LigneVente $ligneVente)
    {
        $ligneVente->load('vente', 'article', 'tarifVerre', 'typeVerre');

        return new LigneVenteResource($ligneVente);
    }

    public function store(StoreLigneVenteRequest $request)
    {
        try {
            $ligne = $this->service->ajouter($request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de l\'ajout de la ligne.', 'errors' => $e->errors()], 422);
        }

        return new LigneVenteResource($ligne);
    }

    public function update(UpdateLigneVenteRequest $request, LigneVente $ligneVente)
    {
        try {
            $ligne = $this->service->modifier($ligneVente, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la modification de la ligne.', 'errors' => $e->errors()], 422);
        }

        return new LigneVenteResource($ligne);
    }

    public function destroy(LigneVente $ligneVente)
    {
        try {
            $this->service->supprimer($ligneVente);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de la ligne.', 'errors' => $e->errors()], 422);
        }

        return response()->noContent();
    }
}