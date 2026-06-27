<?php

namespace App\Http\Controllers;

use App\Http\Requests\MagasinRequest;
use App\Http\Resources\MagasinResource;
use App\Services\MagasinService;
use Illuminate\Http\JsonResponse;

class MagasinController extends Controller
{
    protected $magasinService;

    public function __construct(MagasinService $magasinService)
    {
        $this->magasinService = $magasinService;
    }

    public function index()
    {
        return MagasinResource::collection(
            $this->magasinService->getAll()
        );
    }

    public function show($id)
    {
        return new MagasinResource(
            $this->magasinService->getById($id)
        );
    }

    public function store(MagasinRequest $request): JsonResponse
    {
        $magasin = $this->magasinService->create($request->validated());

        return response()->json([
            'message' => 'Magasin créé avec succès.',
            'data' => new MagasinResource($magasin)
        ], 201);
    }

    public function update(MagasinRequest $request, $id): JsonResponse
    {
        $magasin = $this->magasinService->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Magasin modifié avec succès.',
            'data' => new MagasinResource($magasin)
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->magasinService->delete($id);

        return response()->json([
            'message' => 'Magasin supprimé avec succès.'
        ]);
    }
}