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
        $query = CommandeFournisseur::with(['fournisseur', 'utilisateur', 'lignes.article']);

        if (!empty($filtres['id_fournisseur'])) {
            $query->where('id_fournisseur', $filtres['id_fournisseur']);
        }

        if (!empty($filtres['statut_commande'])) {
            $query->where('statut_commande', $filtres['statut_commande']);
        }

        return $query->orderByDesc('date_commande')->get();
    }

    public function trouver(int $id): CommandeFournisseur
    {
        return CommandeFournisseur::with([
            'fournisseur',
            'utilisateur',
            'lignes.article',
            'lignes.tarifVerre',
        ])->findOrFail($id);
    }

    /**
     * Crée une commande fournisseur avec ses lignes.
     * total_ht calculé depuis prix_unitaire × qte_commandee des lignes.
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

            foreach ($data['lignes'] as $ligneData) {
                LigneCommande::create([
                    'qte_commandee'  => $ligneData['qte_commandee'],
                    'qte_recue'      => 0,
                    'prix_unitaire'  => $ligneData['prix_unitaire'],
                    'id_commande'    => $commande->id_commande,
                    'id_article'     => $ligneData['id_article'] ?? null,
                    'id_tarif_verre' => $ligneData['id_tarif_verre'] ?? null,
                ]);
            }

            $commande->load('lignes');
            $commande->recalculerTotal();

            return $commande->load('fournisseur', 'utilisateur', 'lignes.article');
        });
    }

    /**
     * Modifie l'entête d'une commande (en_attente seulement).
     */
    public function modifier(CommandeFournisseur $commande, array $data): CommandeFournisseur
    {
        $this->verifierEnAttente($commande);

        $commande->update($data);

        return $commande->load('fournisseur', 'lignes.article');
    }

    /**
     * Réceptionne une commande :
     * - met à jour qte_recue sur chaque ligne
     * - crée un MouvementStock "entree" pour la différence reçue
     * - passe le statut à "recue" si toutes les lignes sont complètes
     */
    public function receptionner(CommandeFournisseur $commande, array $data): CommandeFournisseur
    {
        if ($commande->statut_commande === StatutCommande::Annulee) {
            throw ValidationException::withMessages([
                'statut_commande' => 'Une commande annulée ne peut pas être réceptionnée.',
            ]);
        }

        return DB::transaction(function () use ($commande, $data) {
            $commande->load('lignes.article');

            foreach ($data['lignes'] as $ligneData) {
                $ligne = $commande->lignes
                    ->firstWhere('id_ligne_commande', $ligneData['id_ligne_commande']);

                if (!$ligne) continue;

                // Quantité nouvellement reçue dans cette réception
                $nouvelleQte = $ligneData['qte_recue'] - $ligne->qte_recue;

                if ($nouvelleQte > 0 && $ligne->id_article) {
                    $mvt = MouvementStock::create([
                        'type_mvt_stock' => TypeMouvementStock::Entree,
                        'quantite'       => $nouvelleQte,
                        'reference_id'   => $commande->id_commande,
                        'type_reference' => TypeReferenceStock::Commande,
                        'date_mouvement' => now(),
                        'note'           => 'Réception commande n°' . $commande->id_commande,
                        'id_article'     => $ligne->id_article,
                        'id_util'        => $data['id_util'],
                    ]);
                    $mvt->appliquer();
                }

                $ligne->qte_recue = $ligneData['qte_recue'];
                $ligne->save();
            }

            // Recharge les lignes pour vérifier si tout est reçu
            $commande->load('lignes');
            $toutRecu = $commande->lignes->every(
                fn ($l) => $l->qte_recue >= $l->qte_commandee
            );

            if ($toutRecu) {
                $commande->statut_commande = StatutCommande::Recue;
            }

            $commande->date_reception = $data['date_reception'];
            $commande->save();

            return $commande->load('fournisseur', 'utilisateur', 'lignes.article');
        });
    }

    /**
     * Annule une commande (en_attente seulement).
     */
    public function annuler(CommandeFournisseur $commande): CommandeFournisseur
    {
        $this->verifierEnAttente($commande, 'Seules les commandes en attente peuvent être annulées.');

        $commande->statut_commande = StatutCommande::Annulee;
        $commande->save();

        return $commande;
    }

    /**
     * Supprime une commande (en_attente seulement).
     */
    public function supprimer(CommandeFournisseur $commande): void
    {
        $this->verifierEnAttente($commande, 'Seules les commandes en attente peuvent être supprimées.');

        $commande->delete();
    }

    private function verifierEnAttente(
        CommandeFournisseur $commande,
        string $message = 'Seules les commandes en attente sont modifiables.'
    ): void {
        if ($commande->statut_commande !== StatutCommande::EnAttente) {
            throw ValidationException::withMessages([
                'statut_commande' => $message,
            ]);
        }
    }
}