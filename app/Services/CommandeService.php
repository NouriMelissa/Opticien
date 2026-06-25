<?php

namespace App\Services;

use App\Enums\StatutCommande;
use App\Enums\TypeMouvementStock;
use App\Enums\TypeReferenceStock;
use App\Models\CommandeFournisseur;
use App\Models\LigneCommande;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommandeService
{
    public function lister(array $filtres = [])
    {
        $query = CommandeFournisseur::with([
            'fournisseur', 'utilisateur', 'lignes.article', 'lignes.tarifVerre',
        ]);

        if (!empty($filtres['id_fournisseur'])) {
            $query->where('id_fournisseur', $filtres['id_fournisseur']);
        }

        if (!empty($filtres['statut_commande'])) {
            $query->where('statut_commande', $filtres['statut_commande']);
        }

        return $query->orderByDesc('date_commande')->get();
    }

    /**
     * @throws ValidationException si la commande n'existe pas
     */
    public function trouver(int $id): CommandeFournisseur
    {
        $commande = CommandeFournisseur::with([
            'fournisseur', 'utilisateur',
            'lignes.article', 'lignes.tarifVerre',
        ])->find($id);

        if (!$commande) {
            throw ValidationException::withMessages([
                'id_commande' => "Aucune commande trouvée avec l'identifiant $id.",
            ]);
        }

        return $commande;
    }

    /**
     * Crée une commande fournisseur avec ses lignes.
     *
     * @throws ValidationException si une ligne échoue à la création
     */
    public function creer(array $data): CommandeFournisseur
    {
        return DB::transaction(function () use ($data) {
            $commande = CommandeFournisseur::create([
                'date_commande'   => $data['date_commande'],
                'statut_commande' => StatutCommande::EnAttente,
                'total_ht'        => 0,
                'note'            => $data['note'] ?? null,
                'id_fournisseur'  => $data['id_fournisseur'],
                'id_util'         => $data['id_util'],
            ]);

            foreach ($data['lignes'] as $index => $ligneData) {
                try {
                    LigneCommande::create([
                        'qte_commandee'  => $ligneData['qte_commandee'],
                        'qte_recue'      => 0,
                        'prix_unitaire'  => $ligneData['prix_unitaire'],
                        'id_commande'    => $commande->id_commande,
                        'id_article'     => $ligneData['id_article'] ?? null,
                        'id_tarif_verre' => $ligneData['id_tarif_verre'] ?? null,
                    ]);
                } catch (\Throwable $e) {
                    throw ValidationException::withMessages([
                        "lignes.$index" => "Erreur lors de la création de la ligne " . ($index + 1) . " de la commande : " . $e->getMessage(),
                    ]);
                }
            }

            $commande->load('lignes');
            $commande->recalculerTotal();

            return $commande->load('fournisseur', 'utilisateur', 'lignes.article', 'lignes.tarifVerre');
        });
    }

    /**
     * Modifie l'entête d'une commande (en_attente seulement).
     *
     * @throws ValidationException si la commande n'est pas en_attente
     */
    public function modifier(CommandeFournisseur $commande, array $data): CommandeFournisseur
    {
        $this->verifierEnAttente(
            $commande,
            "Impossible de modifier la commande n°{$commande->id_commande} : statut actuel \"{$commande->statut_commande->value}\", seules les commandes en attente sont modifiables."
        );

        $commande->update($data);

        return $commande->load('fournisseur', 'lignes.article');
    }

    /**
     * Réceptionne une commande (partielle ou complète).
     * Met à jour qte_recue, crée les MouvementStock, et passe à "recue" si tout est reçu.
     *
     * @throws ValidationException si commande annulée/déjà reçue, ligne invalide, ou qte incohérente
     */
    public function receptionner(CommandeFournisseur $commande, array $data): CommandeFournisseur
    {
        if ($commande->statut_commande === StatutCommande::Annulee) {
            throw ValidationException::withMessages([
                'statut_commande' => "Impossible de réceptionner la commande n°{$commande->id_commande} : elle est annulée.",
            ]);
        }

        if ($commande->statut_commande === StatutCommande::Recue) {
            throw ValidationException::withMessages([
                'statut_commande' => "La commande n°{$commande->id_commande} est déjà entièrement reçue, aucune réception supplémentaire n'est possible.",
            ]);
        }

        return DB::transaction(function () use ($commande, $data) {
            $commande->load('lignes.article');

            foreach ($data['lignes'] as $index => $ligneData) {
                $ligne = $commande->lignes
                    ->firstWhere('id_ligne_commande', $ligneData['id_ligne_commande']);

                if (!$ligne) {
                    throw ValidationException::withMessages([
                        "lignes.$index.id_ligne_commande" => "La ligne #{$ligneData['id_ligne_commande']} n'appartient pas à la commande n°{$commande->id_commande}.",
                    ]);
                }

                if ($ligneData['qte_recue'] > $ligne->qte_commandee) {
                    throw ValidationException::withMessages([
                        "lignes.$index.qte_recue" => "Ligne #{$ligne->id_ligne_commande} : la quantité reçue ({$ligneData['qte_recue']}) ne peut pas dépasser la quantité commandée ({$ligne->qte_commandee}).",
                    ]);
                }

                if ($ligneData['qte_recue'] < $ligne->qte_recue) {
                    throw ValidationException::withMessages([
                        "lignes.$index.qte_recue" => "Ligne #{$ligne->id_ligne_commande} : la quantité reçue ({$ligneData['qte_recue']}) ne peut pas être inférieure à ce qui a déjà été reçu ({$ligne->qte_recue}).",
                    ]);
                }

                $nouvelleQte = $ligneData['qte_recue'] - $ligne->qte_recue;

                if ($nouvelleQte > 0 && $ligne->id_article) {
                    try {
                        $mvt = MouvementStock::create([
                            'type_mvt_stock' => TypeMouvementStock::Entree,
                            'quantite'       => $nouvelleQte,
                            'reference_id'   => $commande->id_commande,
                            'type_reference' => TypeReferenceStock::Commande,
                            'date_mouvement' => now(),
                            'note'           => "Réception commande n°{$commande->id_commande}",
                            'id_article'     => $ligne->id_article,
                            'id_util'        => $data['id_util'],
                        ]);
                        $mvt->appliquer();
                    } catch (\Throwable $e) {
                        throw ValidationException::withMessages([
                            'stock' => "Erreur lors de l'entrée en stock pour l'article #{$ligne->id_article} (ligne #{$ligne->id_ligne_commande}) : " . $e->getMessage(),
                        ]);
                    }
                }

                $ligne->qte_recue = $ligneData['qte_recue'];
                $ligne->save();
            }

            $commande->load('lignes');
            $toutRecu = $commande->lignes->every(
                fn ($l) => $l->qte_recue >= $l->qte_commandee
            );

            if ($toutRecu) {
                $commande->statut_commande = StatutCommande::Recue;
            }

            $commande->date_reception = $data['date_reception'];
            $commande->save();

            return $commande->load('fournisseur', 'utilisateur', 'lignes.article', 'lignes.tarifVerre');
        });
    }

    /**
     * Annule une commande (en_attente seulement).
     *
     * @throws ValidationException si la commande n'est pas en_attente
     */
    public function annuler(CommandeFournisseur $commande): CommandeFournisseur
    {
        $this->verifierEnAttente(
            $commande,
            "Impossible d'annuler la commande n°{$commande->id_commande} : seules les commandes en attente peuvent être annulées (statut actuel : {$commande->statut_commande->value})."
        );

        $commande->statut_commande = StatutCommande::Annulee;
        $commande->save();

        return $commande->load('fournisseur', 'utilisateur');
    }

    /**
     * Supprime une commande (en_attente seulement).
     *
     * @throws ValidationException si la commande n'est pas en_attente
     */
    public function supprimer(CommandeFournisseur $commande): void
    {
        $this->verifierEnAttente(
            $commande,
            "Impossible de supprimer la commande n°{$commande->id_commande} : seules les commandes en attente peuvent être supprimées (statut actuel : {$commande->statut_commande->value})."
        );

        $commande->delete();
    }

    private function verifierEnAttente(
        CommandeFournisseur $commande,
        ?string $message = null
    ): void {
        if ($commande->statut_commande !== StatutCommande::EnAttente) {
            throw ValidationException::withMessages([
                'statut_commande' => $message ?? "Cette action n'est possible que sur une commande en attente (statut actuel : {$commande->statut_commande->value}).",
            ]);
        }
    }
}