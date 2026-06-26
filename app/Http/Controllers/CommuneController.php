<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommuneRequest;
use App\Http\Requests\UpdateCommuneRequest;
use App\Http\Requests\SupprimerCommuneRequest;
use App\Http\Resources\CommuneResource;
use App\Services\CommuneService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommuneController extends Controller
{
    public function __construct(private CommuneService $service)
    {
    }

    // GET /api/v1/communes?search=...&id_wilaya=...
    public function index(Request $request): JsonResponse
    {
        $communes = $this->service->lister($request->only(['search', 'id_wilaya']));

        return response()->json([
            'success' => true,
            'data'    => CommuneResource::collection($communes),
        ]);
    }

    // POST /api/v1/communes
    public function store(StoreCommuneRequest $request): JsonResponse
    {
        $commune = $this->service->creer($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Commune créée avec succès.',
            'data'    => new CommuneResource($commune),
        ], 201);
    }

    // GET /api/v1/communes/{id}
    public function show(int $id): JsonResponse
    {
        $commune = $this->service->trouver($id);

        return response()->json([
            'success' => true,
            'data'    => new CommuneResource($commune),
        ]);
    }

    // PUT /api/v1/communes/{id}
    public function update(UpdateCommuneRequest $request, int $id): JsonResponse
    {
        $commune = $this->service->trouver($id);
        $commune = $this->service->modifier($commune, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Commune mise à jour avec succès.',
            'data'    => new CommuneResource($commune),
        ]);
    }

    // DELETE /api/v1/communes/{id}
    public function destroy(SupprimerCommuneRequest $request, int $id): JsonResponse
    {
        $commune = $this->service->trouver($id);
        $this->service->supprimer($commune);

        return response()->json([
            'success' => true,
            'message' => 'Commune supprimée avec succès.',
        ]);
    }

    // GET /api/v1/communes/dropdown?id_wilaya=16
    public function dropdown(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->dropdown($request->only(['id_wilaya'])),
        ]);
    }
}