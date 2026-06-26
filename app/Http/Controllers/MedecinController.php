<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedecinRequest;
use App\Http\Requests\UpdateMedecinRequest;
use App\Http\Requests\SupprimerMedecinRequest;
use App\Http\Resources\MedecinResource;
use App\Models\Medecin;
use App\Services\MedecinService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedecinController extends Controller
{
    public function __construct(private MedecinService $service)
    {
    }

    // GET /api/v1/medecins?search=...&specialite=...&id_wilaya=...
    public function index(Request $request): JsonResponse
    {
        $medecins = $this->service->lister($request->only(['search', 'specialite', 'id_wilaya']));

        return response()->json([
            'success' => true,
            'data'    => MedecinResource::collection($medecins),
        ]);
    }

    // POST /api/v1/medecins
    public function store(StoreMedecinRequest $request): JsonResponse
    {
        $medecin = $this->service->creer($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Médecin créé avec succès.',
            'data'    => new MedecinResource($medecin),
        ], 201);
    }

    // GET /api/v1/medecins/{id}
    public function show(int $id): JsonResponse
    {
        $medecin = $this->service->trouver($id);

        return response()->json([
            'success' => true,
            'data'    => new MedecinResource($medecin),
        ]);
    }

    // PUT /api/v1/medecins/{id}
    public function update(UpdateMedecinRequest $request, int $id): JsonResponse
    {
        $medecin = $this->service->trouver($id);
        $medecin = $this->service->modifier($medecin, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Médecin mis à jour avec succès.',
            'data'    => new MedecinResource($medecin),
        ]);
    }

    // DELETE /api/v1/medecins/{id}
    public function destroy(SupprimerMedecinRequest $request, int $id): JsonResponse
    {
        $medecin = $this->service->trouver($id);
        $this->service->supprimer($medecin);

        return response()->json([
            'success' => true,
            'message' => 'Médecin supprimé avec succès.',
        ]);
    }

    // GET /api/v1/medecins/dropdown
    public function dropdown(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->dropdown(),
        ]);
    }
}