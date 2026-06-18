<?php

namespace App\Services;

use App\Models\Commune;
use Illuminate\Support\Facades\DB;

class CommuneService
{
    /**
     * Liste des communes avec filtres optionnels
     * Filtres : search (nom), id_wilaya
     */
    public function lister(array $filtres = [])
    {
        $query = Commune::with('wilaya');

        if (!empty($filtres['search'])) {
            $query->searchNom($filtres['search']);
        }

        if (!empty($filtres['id_wilaya'])) {
            $query->byWilaya((int) $filtres['id_wilaya']);
        }

        return $query->orderBy('nom_commune')->get();
    }

    public function trouver(int $id): Commune
    {
        return Commune::with('wilaya')->findOrFail($id);
    }

    public function creer(array $data): Commune
    {
        return DB::transaction(function () use ($data) {
            $commune = Commune::create([
                'nom_commune' => $data['nom_commune'],
                'id_wilaya'   => $data['id_wilaya'],
            ]);

            return $commune->load('wilaya');
        });
    }

    public function modifier(Commune $commune, array $data): Commune
    {
        $commune->update($data);

        return $commune->load('wilaya');
    }

    /**
     * Suppression simple — pas de dépendance bloquante connue pour l'instant.
     * (Si plus tard Client référence une commune par FK stricte, ajouter la vérification ici.)
     */
    public function supprimer(Commune $commune): void
    {
        $commune->delete();
    }

    /**
     * Liste légère pour dropdown Angular
     * Utilisée typiquement filtrée par wilaya : dropdown(['id_wilaya' => 16])
     */
    public function dropdown(array $filtres = [])
    {
        $query = Commune::select('id_commune', 'nom_commune', 'id_wilaya');

        if (!empty($filtres['id_wilaya'])) {
            $query->byWilaya((int) $filtres['id_wilaya']);
        }

        return $query->orderBy('nom_commune')->get();
    }
}