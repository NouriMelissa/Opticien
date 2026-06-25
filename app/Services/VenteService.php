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

    /**
     * @throws ValidationException si la vente n'existe pas
     */
    public function trouver(int $id): Vente
    {
        $vente = Vente::with([
            'client', 'session', 'ordonnance',
            'ligneVentes.article',
            'ligneVentes.tarifVerre',
            'ligneVentes.typeVerre',
            'paiements', 'echeances', 'factures',
        ])->find($id);

        if (!$vente) {
            throw ValidationException::withMessages([
                'id_vente' => "Aucune vente trouvée avec l'identifiant $id.",
            ]);
        }

        return $vente;
    }

    /**
     * Crée une vente en brouillon avec ses lignes.
     *
     * @throws ValidationException si une ligne échoue à la création
     */
    public function creer(array $data): Vente
    {
        return DB::transaction(function () use ($data) {
            $vente = Vente::create([
                'date_vente'            => now(),
                'remise_globale_pct'    => $data['remise_globale_pct'] ?? 0,
                'mode_paiement'         => $data['mode_paiement'],
                'date_livraison_prevue' => $data['date_livraison_prevue'] ?? null,
                'statut_vente'          => StatutVente::Brouillon,
                'note'                  => $data['note'] ?? null,
                'id_client'             => $data['id_client'],
                'id_session'            => $data['id_session'],
                'id_ordonnance'         => $data['id_ordonnance'] ?? null,
            ]);

            foreach ($data['lignes'] as $index => $ligneData) {
                try {
                    $ligne = new LigneVente($ligneData);
                    $ligne->id_vente = $vente->id_vente;
                    $ligne->calculerTotal();
                    $ligne->save();
                } catch (\Throwable $e) {
                    throw ValidationException::withMessages([
                        "lignes.$index" => "Erreur lors de la création de la ligne " . ($index + 1) . " (type: " . ($ligneData['type_ligne'] ?? '?') . ") : " . $e->getMessage(),
                    ]);
                }
            }

            $this->recalculer($vente);

            return $vente->load(
                'client', 'ligneVentes.article',
                'ligneVentes.tarifVerre', 'ligneVentes.typeVerre'
            );
        });
    }

    /**
     * Modifie l'entête d'une vente en brouillon.
     *
     * @throws ValidationException si la vente n'est pas en brouillon
     */
    public function modifier(Vente $vente, array $data): Vente
    {
        $this->verifierBrouillon(
            $vente,
            "Impossible de modifier la vente n°{$vente->id_vente} : elle n'est plus en brouillon (statut actuel : {$vente->statut_vente->value})."
        );

        $vente->update($data);
        $this->recalculer($vente);

        return $vente->load('client', 'ligneVentes.article');
    }

    /**
     * Valide la vente : passe le statut à "validee" et décrémente le stock.
     *
     * @throws ValidationException si la vente n'est pas en brouillon ou si stock insuffisant
     */
    public function valider(Vente $vente, int $idUtil): Vente
    {
        $this->verifierBrouillon(
            $vente,
            "Impossible de valider la vente n°{$vente->id_vente} : elle n'est plus en brouillon (statut actuel : {$vente->statut_vente->value})."
        );

        return DB::transaction(function () use ($vente, $idUtil) {
            $vente->load('ligneVentes.article');

            foreach ($vente->ligneVentes as $ligne) {
                if ($ligne->id_article && $ligne->article) {
                    if ($ligne->article->stock_actuel < $ligne->qte) {
                        throw ValidationException::withMessages([
                            'stock' => "Stock insuffisant pour l'article '{$ligne->article->modele}' (réf: {$ligne->article->reference}) : stock disponible {$ligne->article->stock_actuel}, quantité demandée {$ligne->qte}.",
                        ]);
                    }

                    try {
                        $this->creerMouvementStock(
                            $ligne, $vente,
                            TypeMouvementStock::Sortie,
                            $idUtil,
                            "Vente n°{$vente->id_vente}"
                        );
                    } catch (\Throwable $e) {
                        throw ValidationException::withMessages([
                            'stock' => "Erreur lors de la sortie de stock pour l'article '{$ligne->article->modele}' (ligne n°{$ligne->id_ligne_vente}) : " . $e->getMessage(),
                        ]);
                    }
                }
            }

            $vente->statut_vente = StatutVente::Validee;
            $vente->save();

            return $vente->load('client', 'ligneVentes.article');
        });
    }

    /**
     * Annule la vente : restitue le stock si elle était validée.
     *
     * @throws ValidationException si la vente est déjà annulée
     */
    public function annuler(Vente $vente, int $idUtil): Vente
    {
        if ($vente->statut_vente === StatutVente::Annulee) {
            throw ValidationException::withMessages([
                'statut_vente' => "La vente n°{$vente->id_vente} est déjà annulée, impossible de l'annuler une seconde fois.",
            ]);
        }

        return DB::transaction(function () use ($vente, $idUtil) {
            if ($vente->statut_vente === StatutVente::Validee) {
                $vente->load('ligneVentes.article');

                foreach ($vente->ligneVentes as $ligne) {
                    if ($ligne->id_article) {
                        try {
                            $this->creerMouvementStock(
                                $ligne, $vente,
                                TypeMouvementStock::Retour,
                                $idUtil,
                                "Annulation vente n°{$vente->id_vente}"
                            );
                        } catch (\Throwable $e) {
                            throw ValidationException::withMessages([
                                'stock' => "Erreur lors de la restitution du stock pour l'article #{$ligne->id_article} (ligne n°{$ligne->id_ligne_vente}) : " . $e->getMessage(),
                            ]);
                        }
                    }
                }
            }

            $vente->statut_vente = StatutVente::Annulee;
            $vente->save();

            return $vente->load('client', 'ligneVentes.article');
        });
    }

    /**
     * Supprime une vente en brouillon.
     *
     * @throws ValidationException si la vente n'est pas en brouillon
     */
    public function supprimer(Vente $vente): void
    {
        $this->verifierBrouillon(
            $vente,
            "Impossible de supprimer la vente n°{$vente->id_vente} : seules les ventes en brouillon peuvent être supprimées (statut actuel : {$vente->statut_vente->value})."
        );

        $vente->delete();
    }

    private function recalculer(Vente $vente): void
    {
        $vente->load('ligneVentes');
        $vente->recalculerTotal();
        $vente->save();
    }

    private function verifierBrouillon(Vente $vente, ?string $message = null): void
    {
        if ($vente->statut_vente !== StatutVente::Brouillon) {
            throw ValidationException::withMessages([
                'statut_vente' => $message ?? "Cette action n'est possible que sur une vente en brouillon (statut actuel : {$vente->statut_vente->value}).",
            ]);
        }
    }

    private function creerMouvementStock(
        LigneVente $ligne,
        Vente $vente,
        TypeMouvementStock $type,
        int $idUtil,
        string $note
    ): void {
        $mvt = MouvementStock::create([
            'type_mvt_stock' => $type,
            'quantite'       => $ligne->qte,
            'reference_id'   => $vente->id_vente,
            'type_reference' => TypeReferenceStock::Vente,
            'date_mouvement' => now(),
            'note'           => $note,
            'id_article'     => $ligne->id_article,
            'id_util'        => $idUtil,
        ]);

        $mvt->appliquer();
    }
}