<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWilayaRequest;
use App\Http\Requests\UpdateWilayaRequest;
use App\Http\Requests\SupprimerWilayaRequest;
use App\Http\Resources\WilayaResource;
use App\Services\WilayaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Wilaya;
class WilayaController extends Controller
{
    public function __construct(private WilayaService $service)
    {
    }

    // GET /api/v1/wilayas?search=...
    public function index(Request $request): JsonResponse
    {
        $wilayas = $this->service->lister($request->only(['search']));

        return response()->json([
            'success' => true,
            'data'    => WilayaResource::collection($wilayas),
        ]);
    }

    // POST /api/v1/wilayas
public function store(StoreWilayaRequest $request): JsonResponse
{
    $wilaya = $this->service->creer($request->validated());

    return response()->json([
        'success' => true,
        'data' => new WilayaResource($wilaya),
    ], 201);
}

    // GET /api/v1/wilayas/{id}
    // public function show(int $id): JsonResponse
    // {
    //     $wilaya = $this->service->trouver($id);

    //     return response()->json([
    //         'success' => true,
    //         'data'    => new WilayaResource($wilaya),
    //     ]);
    // }
public function show(Wilaya $wilaya): JsonResponse
{
    return response()->json([
        'success' => true,
        'data' => new WilayaResource($wilaya),
    ]);
}
    // PUT /api/v1/wilayas/{id}
    // public function update(UpdateWilayaRequest $request, int $id): JsonResponse
    // {
    //     $wilaya = $this->service->trouver($id);
    //     $wilaya = $this->service->modifier($wilaya, $request->validated());

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Wilaya mise à jour avec succès.',
    //         'data'    => new WilayaResource($wilaya),
    //     ]);
    // }


    public function update(UpdateWilayaRequest $request, Wilaya $wilaya): JsonResponse
{
    $wilaya = $this->service->modifier($wilaya, $request->validated());

    return response()->json([
        'success' => true,
        'message' => 'Wilaya mise à jour avec succès.',
        'data' => new WilayaResource($wilaya),
    ]);
}
    // DELETE /api/v1/wilayas/{id}
    // public function destroy(SupprimerWilayaRequest $request, int $id): JsonResponse
    // {
    //     $wilaya = $this->service->trouver($id);
    //     $this->service->supprimer($wilaya);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Wilaya supprimée avec succès.',
    //     ]);
    // }


    public function destroy(SupprimerWilayaRequest $request, Wilaya $wilaya): JsonResponse
{
    $this->service->supprimer($wilaya);

    return response()->json([
        'success' => true,
        'message' => 'Wilaya supprimée avec succès.',
    ]);
}
    // GET /api/v1/wilayas/dropdown
    public function dropdown(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->dropdown(),
        ]);
    }
}