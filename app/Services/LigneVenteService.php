<?php

namespace App\Services;

use App\Enums\StatutVente;
use App\Models\LigneVente;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LigneVenteService
{
    public function lister(int $idVente)
    {
        return LigneVente::with(['article', 'tarifVerre', 'typeVerre'])
            ->where('id_vente', $idVente)
            ->get();
    }

    public function trouver(int $id): LigneVente
    {
        return LigneVente::with(['vente', 'article', 'tarifVerre', 'typeVerre'])
            ->findOrFail($id);
    }

    /**
     * Ajoute une ligne à une vente brouillon et recalcule son total.
     */
    public function ajouter(array $data): LigneVente
    {
        $vente = Vente::findOrFail($data['id_vente']);
        $this->verifierBrouillon($vente);

        return DB::transaction(function () use ($data, $vente) {
            $ligne = new LigneVente($data);
            $ligne->calculerTotal();
            $ligne->save();

            $this->recalculerVente($vente);

            return $ligne->load('article', 'tarifVerre', 'typeVerre');
        });
    }

    /**
     * Modifie une ligne et recalcule le total de la vente.
     */
    public function modifier(LigneVente $ligne, array $data): LigneVente
    {
        $vente = Vente::findOrFail($ligne->id_vente);
        $this->verifierBrouillon($vente);

        return DB::transaction(function () use ($ligne, $data, $vente) {
            $ligne->fill($data);
            $ligne->calculerTotal();
            $ligne->save();

            $this->recalculerVente($vente);

            return $ligne->load('article', 'tarifVerre', 'typeVerre');
        });
    }

    /**
     * Supprime une ligne et recalcule le total de la vente.
     */
    public function supprimer(LigneVente $ligne): void
    {
        $vente = Vente::findOrFail($ligne->id_vente);
        $this->verifierBrouillon($vente);

        DB::transaction(function () use ($ligne, $vente) {
            $ligne->delete();
            $this->recalculerVente($vente);
        });
    }

    private function recalculerVente(Vente $vente): void
    {
        $vente->load('ligneVentes');
        $vente->recalculerTotal();
        $vente->save();
    }

    private function verifierBrouillon(Vente $vente): void
    {
        if ($vente->statut_vente !== StatutVente::Brouillon) {
            throw ValidationException::withMessages([
                'statut_vente' => 'Les lignes ne sont modifiables que sur une vente en brouillon.',
            ]);
        }
    }
}