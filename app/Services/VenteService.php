<?php

namespace App\Services;

use App\Enums\StatutVente;
use App\Enums\TypeMouvementStock;
use App\Enums\TypeReferenceStock;
use App\Models\LigneVente;
use App\Models\MouvementStock;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VenteService
{
    public function lister(array $filtres = [])
    {
        $query = Vente::with(['client', 'ligneVentes']);

        if (!empty($filtres['id_client'])) {
            $query->where('id_client', $filtres['id_client']);
        }

        if (!empty($filtres['id_session'])) {
            $query->where('id_session', $filtres['id_session']);
        }

        if (!empty($filtres['statut_vente'])) {
            $query->where('statut_vente', $filtres['statut_vente']);
        }

        return $query->orderByDesc('date_vente')->get();
    }

    public function trouver(int $id): Vente
    {
        return Vente::with([
            'client', 'session', 'ordonnance',
            'ligneVentes.article', 'ligneVentes.tarifVerre', 'ligneVentes.typeVerre',
            'paiements', 'echeances', 'factures',
        ])->findOrFail($id);
    }

    public function creer(array $data): Vente
    {
        return DB::transaction(function () use ($data) {
            $vente = Vente::create([
                'date_vente' => now(),
                'remise_globale_pct' => $data['remise_globale_pct'] ?? 0,
                'mode_paiement' => $data['mode_paiement'],
                'date_livraison_prevue' => $data['date_livraison_prevue'] ?? null,
                'statut_vente' => StatutVente::Brouillon,
                'note' => $data['note'] ?? null,
                'id_client' => $data['id_client'],
                'id_session' => $data['id_session'],
                'id_ordonnance' => $data['id_ordonnance'] ?? null,
            ]);

            foreach ($data['lignes'] as $ligneData) {
                $ligne = new LigneVente($ligneData);
                $ligne->id_vente = $vente->id_vente;
                $ligne->calculerTotal();
                $ligne->save();
            }

            $this->recalculer($vente);

            return $vente->load('ligneVentes.article', 'ligneVentes.tarifVerre', 'ligneVentes.typeVerre', 'client');
        });
    }

    public function modifier(Vente $vente, array $data): Vente
    {
        $this->verifierBrouillon($vente);

        $vente->update($data);
        $this->recalculer($vente);

        return $vente->load('ligneVentes.article', 'client');
    }

    public function valider(Vente $vente, int $idUtil): Vente
    {
        $this->verifierBrouillon($vente, 'Cette vente n\'est plus en brouillon');

        return DB::transaction(function () use ($vente, $idUtil) {
            $vente->load('ligneVentes.article');

            foreach ($vente->ligneVentes as $ligne) {
                if ($ligne->id_article) {
                    $this->creerMouvementStock(
                        $ligne, $vente, TypeMouvementStock::Sortie, $idUtil, 'Vente n°' . $vente->id_vente
                    );
                }
            }

            $vente->statut_vente = StatutVente::Validee;
            $vente->save();

            return $vente->load('ligneVentes.article', 'client');
        });
    }

    public function annuler(Vente $vente, int $idUtil): Vente
    {
        if ($vente->statut_vente === StatutVente::Annulee) {
            throw ValidationException::withMessages(['vente' => 'Vente déjà annulée']);
        }

        return DB::transaction(function () use ($vente, $idUtil) {
            if ($vente->statut_vente === StatutVente::Validee) {
                $vente->load('ligneVentes.article');

                foreach ($vente->ligneVentes as $ligne) {
                    if ($ligne->id_article) {
                        $this->creerMouvementStock(
                            $ligne, $vente, TypeMouvementStock::Retour, $idUtil, 'Annulation vente n°' . $vente->id_vente
                        );
                    }
                }
            }

            $vente->statut_vente = StatutVente::Annulee;
            $vente->save();

            return $vente->load('ligneVentes.article', 'client');
        });
    }

    public function supprimer(Vente $vente): void
    {
        $this->verifierBrouillon($vente, 'Seules les ventes en brouillon peuvent être supprimées');

        $vente->delete();
    }

    private function recalculer(Vente $vente): void
    {
        $vente->load('ligneVentes');
        $vente->recalculerTotal();
        $vente->save();
    }

    private function verifierBrouillon(Vente $vente, string $message = 'Seules les ventes en brouillon sont modifiables'): void
    {
        if ($vente->statut_vente !== StatutVente::Brouillon) {
            throw ValidationException::withMessages(['statut_vente' => $message]);
        }
    }

    // private function creerMouvementStock(LigneVente $ligne, Vente $vente, TypeMouvementStock $type, int $idUtil, string $note): void
    // {
    //     $mvt = MouvementStock::create([
    //         'type_mvt_stock' => $type,
    //         'quantite' => $ligne->qte,
    //         'reference_id' => $vente->id_vente,
    //         'type_reference' => TypeReferenceStock::Vente,
    //         'date_mouvement' => now(),
    //         'note' => $note,
    //         'id_article' => $ligne->id_article,
    //         'id_util' => $idUtil,
    //     ]);

    //     $mvt->appliquer();
    // }
}