<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Requests\SupprimerClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(private ClientService $service)
    {
    }

    // GET /api/v1/clients?search=...&id_wilaya=...&tel=...&per_page=15
    public function index(Request $request): JsonResponse
    {
        $clients = $this->service->lister($request->only(['search', 'id_wilaya', 'tel', 'per_page']));

        return response()->json([
            'success' => true,
            'data'    => ClientResource::collection($clients),
            'meta'    => [
                'current_page' => $clients->currentPage(),
                'last_page'    => $clients->lastPage(),
                'total'        => $clients->total(),
            ],
        ]);
    }

    // POST /api/v1/clients
    public function store(StoreClientRequest $request): JsonResponse
    {
        // idUtilCreateur viendrait normalement de auth()->id() une fois l'auth branchée
        $client = $this->service->creer($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Client créé avec succès.',
            'data'    => new ClientResource($client),
        ], 201);
    }

    // GET /api/v1/clients/{id}
    public function show(int $id): JsonResponse
    {
        $client = $this->service->trouver($id);

        return response()->json([
            'success' => true,
            'data'    => new ClientResource($client),
        ]);
    }

    // PUT /api/v1/clients/{id}
    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = $this->service->trouver($id);
        $client = $this->service->modifier($client, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Client mis à jour avec succès.',
            'data'    => new ClientResource($client),
        ]);
    }

    // DELETE /api/v1/clients/{id}
    public function destroy(SupprimerClientRequest $request, int $id): JsonResponse
    {
        $client = $this->service->trouver($id);
        $this->service->supprimer($client);

        return response()->json([
            'success' => true,
            'message' => 'Client supprimé avec succès.',
        ]);
    }

    // GET /api/v1/clients/dropdown?search=...
    public function dropdown(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->service->dropdown($request->get('search', '')),
        ]);
    }
}