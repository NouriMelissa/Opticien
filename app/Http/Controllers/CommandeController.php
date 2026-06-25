<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceptionCommandeRequest;
use App\Http\Requests\StoreCommandeRequest;
use App\Http\Requests\UpdateCommandeRequest;
use App\Http\Resources\CommandeResource;
use App\Models\CommandeFournisseur;
use App\Services\CommandeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommandeController extends Controller
{
    public function __construct(private CommandeService $service) {}

    public function index(Request $request)
    {
        $commandes = $this->service->lister(
            $request->only('id_fournisseur', 'statut_commande')
        );

        return CommandeResource::collection($commandes);
    }

    public function show(CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->trouver($commande->id_commande);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Commande introuvable.', 'errors' => $e->errors()], 404);
        }

        return new CommandeResource($commande);
    }

    public function store(StoreCommandeRequest $request)
    {
        try {
            $commande = $this->service->creer($request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la création.', 'errors' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    public function update(UpdateCommandeRequest $request, CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->modifier($commande, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la modification.', 'errors' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    public function receptionner(ReceptionCommandeRequest $request, CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->receptionner($commande, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la réception.', 'errors' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    public function annuler(CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->annuler($commande);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de l\'annulation.', 'errors' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    public function destroy(CommandeFournisseur $commande)
    {
        try {
            $this->service->supprimer($commande);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erreur lors de la suppression.', 'errors' => $e->errors()], 422);
        }

        return response()->noContent();
    }
}