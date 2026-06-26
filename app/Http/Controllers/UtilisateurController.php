<?php

namespace App\Http\Controllers;

use App\Services\UtilisateurService;

use App\Http\Requests\Utilisateur\StoreUtilisateurRequest;
use App\Http\Requests\Utilisateur\UpdateUtilisateurRequest;

use App\Http\Resources\UtilisateurResource;

class UtilisateurController extends Controller
{
    private UtilisateurService $service;

    public function __construct(UtilisateurService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return UtilisateurResource::collection(
            $this->service->getAll()
        );
    }

    
    public function show($id)
    {
        return new UtilisateurResource(
            $this->service->getById($id)
        );
    }

    
    public function store(
        StoreUtilisateurRequest $request
    )
    {
        $user = $this->service->create(
            $request->validated()
        );

        return new UtilisateurResource(
            $user
        );
    }

   
    public function update(
        UpdateUtilisateurRequest $request,
        $id
    )
    {
        $user = $this->service->update(
            $id,
            $request->validated()
        );

        return new UtilisateurResource(
            $user
        );
    }

   
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'message' =>
                'Utilisateur supprimé avec succès'
        ]);
    }
}