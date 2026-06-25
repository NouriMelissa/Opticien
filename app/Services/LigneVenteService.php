<?php

namespace App\Services;

use App\Enums\StatutVente;
use App\Models\LigneVente;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LigneVenteService
{
    /**
     * Liste les lignes d'une vente.
     *
     * @throws ValidationException si la vente n'existe pas
     */
    public function lister(int $idVente)
    {
        $vente = Vente::find($idVente);

        if (!$vente) {
            throw ValidationException::withMessages([
                'id_vente' => "La vente n°$idVente n'existe pas.",
            ]);
        }

        return LigneVente::with(['article', 'tarifVerre', 'typeVerre'])
            ->where('id_vente', $idVente)
            ->get();
    }

    /**
     * @throws ValidationException si la ligne n'existe pas
     */
    public function trouver(int $id): LigneVente
    {
        $ligne = LigneVente::with(['vente', 'article', 'tarifVerre', 'typeVerre'])->find($id);

        if (!$ligne) {
            throw ValidationException::withMessages([
                'id_ligne_vente' => "Aucune ligne de vente trouvée avec l'identifiant $id.",
            ]);
        }

        return $ligne;
    }

    /**
     * Ajoute une ligne à une vente en brouillon et recalcule le total.
     *
     * @throws ValidationException si la vente n'existe pas, n'est pas en brouillon, ou la ligne échoue
     */
    public function ajouter(array $data): LigneVente
    {
        $vente = Vente::find($data['id_vente']);

        if (!$vente) {
            throw ValidationException::withMessages([
                'id_vente' => "La vente n°{$data['id_vente']} n'existe pas. Impossible d'ajouter une ligne.",
            ]);
        }

        $this->verifierBrouillon($vente);

        return DB::transaction(function () use ($data, $vente) {
            try {
                $ligne = new LigneVente($data);
                $ligne->calculerTotal();
                $ligne->save();
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'ligne' => "Erreur lors de l'ajout de la ligne à la vente n°{$vente->id_vente} : " . $e->getMessage(),
                ]);
            }

            $this->recalculerVente($vente);

            return $ligne->load('article', 'tarifVerre', 'typeVerre');
        });
    }

    /**
     * Modifie une ligne existante et recalcule le total de la vente.
     *
     * @throws ValidationException si la vente n'existe pas, n'est pas en brouillon, ou la modification échoue
     */
    public function modifier(LigneVente $ligne, array $data): LigneVente
    {
        $vente = Vente::find($ligne->id_vente);

        if (!$vente) {
            throw ValidationException::withMessages([
                'id_vente' => "La vente associée à la ligne n°{$ligne->id_ligne_vente} n'existe plus.",
            ]);
        }

        $this->verifierBrouillon($vente);

        return DB::transaction(function () use ($ligne, $data, $vente) {
            try {
                $ligne->fill($data);
                $ligne->calculerTotal();
                $ligne->save();
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'ligne' => "Erreur lors de la modification de la ligne n°{$ligne->id_ligne_vente} : " . $e->getMessage(),
                ]);
            }

            $this->recalculerVente($vente);

            return $ligne->load('article', 'tarifVerre', 'typeVerre');
        });
    }

    /**
     * Supprime une ligne et recalcule le total de la vente.
     *
     * @throws ValidationException si la vente n'existe pas ou n'est pas en brouillon
     */
    public function supprimer(LigneVente $ligne): void
    {
        $vente = Vente::find($ligne->id_vente);

        if (!$vente) {
            throw ValidationException::withMessages([
                'id_vente' => "La vente associée à la ligne n°{$ligne->id_ligne_vente} n'existe plus.",
            ]);
        }

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
                'statut_vente' => "Les lignes ne sont modifiables que sur une vente en brouillon (vente n°{$vente->id_vente}, statut actuel : {$vente->statut_vente->value}).",
            ]);
        }
    }
}