<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVenteRequest;
use App\Http\Requests\UpdateVenteRequest;
use App\Http\Requests\ValiderVenteRequest;
use App\Http\Resources\VenteResource;
use App\Models\Vente;
use App\Services\VenteService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VenteController extends Controller
{
    public function __construct(private VenteService $venteService) {}

    public function index(Request $request)
    {
        $ventes = $this->venteService->lister(
            $request->only('id_client', 'id_session', 'statut_vente')
        );

        return VenteResource::collection($ventes);
    }

    public function show($id)
    {
        try {
            $vente = $this->venteService->trouver($id);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Vente introuvable.', 'errors' => $e->errors()], 404);
        }

        return new VenteResource($vente);
    }

    public function store(StoreVenteRequest $request)
    {
        try {
            $vente = $this->venteService->creer($request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la création.', 'errors' => $e->errors()], 422);
        }

        return new VenteResource($vente);
    }

    public function update(UpdateVenteRequest $request, Vente $vente)
    {
        try {
            $vente = $this->venteService->modifier($vente, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la modification.', 'errors' => $e->errors()], 422);
        }

        return new VenteResource($vente);
    }

    public function valider(ValiderVenteRequest $request, Vente $vente)
    {
        try {
            $vente = $this->venteService->valider($vente, $request->id_util);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la validation.', 'errors' => $e->errors()], 422);
        }

        return new VenteResource($vente);
    }

    public function annuler(ValiderVenteRequest $request, Vente $vente)
    {
        try {
            $vente = $this->venteService->annuler($vente, $request->id_util);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de l\'annulation.', 'errors' => $e->errors()], 422);
        }

        return new VenteResource($vente);
    }

    public function destroy(Vente $vente)
    {
        try {
            $this->venteService->supprimer($vente);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la suppression.', 'errors' => $e->errors()], 422);
        }

        return response()->noContent();
    }
}