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

    /**
     * GET /api/commandes
     */
    public function index(Request $request)
    {
        $commandes = $this->service->lister(
            $request->only('id_fournisseur', 'statut_commande')
        );

        return CommandeResource::collection($commandes);
    }

    /**
     * GET /api/commandes/{commande}
     */
    public function show(CommandeFournisseur $commande)
    {
        return new CommandeResource(
            $this->service->trouver($commande->id_commande)
        );
    }

    /**
     * POST /api/commandes
     */
    public function store(StoreCommandeRequest $request)
    {
        $commande = $this->service->creer($request->validated());

        return new CommandeResource($commande);
    }

    /**
     * PUT /api/commandes/{commande}
     */
    public function update(UpdateCommandeRequest $request, CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->modifier($commande, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    /**
     * POST /api/commandes/{commande}/receptionner
     */
    public function receptionner(ReceptionCommandeRequest $request, CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->receptionner($commande, $request->validated());
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    /**
     * POST /api/commandes/{commande}/annuler
     */
    public function annuler(CommandeFournisseur $commande)
    {
        try {
            $commande = $this->service->annuler($commande);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return new CommandeResource($commande);
    }

    /**
     * DELETE /api/commandes/{commande}
     */
    public function destroy(CommandeFournisseur $commande)
    {
        try {
            $this->service->supprimer($commande);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        }

        return response()->noContent();
    }
}