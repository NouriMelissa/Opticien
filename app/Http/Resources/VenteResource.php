<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_vente'            => $this->id_vente,
            'date_vente'          => $this->date_vente,
            'total_avant_remise'  => (float) $this->total_avant_remise,
            'remise_globale_pct'  => (float) $this->remise_globale_pct,
            'remise_globale_mnt'  => (float) $this->remise_globale_mnt,
            'total_ttc'           => (float) $this->total_ttc,
            'montant_encaisse'    => (float) $this->montant_encaisse,
            'reste_a_payer'       => (float) $this->reste_a_payer,
            'mode_paiement'       => $this->mode_paiement->value,
            'date_livraison_prevue' => $this->date_livraison_prevue,
            'livree'              => (bool) $this->livree,
            'statut_vente'        => $this->statut_vente->value,
            'statut_vente_label'  => $this->statut_vente->label(),
            'note'                => $this->note,
            'id_client'           => $this->id_client,
            'id_session'          => $this->id_session,
            'id_ordonnance'       => $this->id_ordonnance,

            'client' => $this->whenLoaded('client', fn () => [
                'id_client'    => $this->client->id_client,
                'nom_prenom'   => $this->client->nom_prenom,
                'tel_portable' => $this->client->tel_portable,
            ]),

            'session' => $this->whenLoaded('session', fn () => [
                'id_session'    => $this->session->id_session,
                'date_ouverture' => $this->session->date_ouverture,
                'statut'        => $this->session->statut,
            ]),

            'ordonnance' => $this->whenLoaded('ordonnance', fn () => [
                'id_ordonnance'     => $this->ordonnance->id_ordonnance,
                'date_prescription' => $this->ordonnance->date_prescription,
            ]),

            'lignes' => $this->whenLoaded(
                'ligneVentes',
                fn () => LigneVenteResource::collection($this->ligneVentes)
            ),

            'paiements' => $this->whenLoaded(
                'paiements',
                fn () => PaiementResource::collection($this->paiements)
            ),

            'echeances' => $this->whenLoaded(
                'echeances',
                fn () => EcheanceResource::collection($this->echeances)
            ),

            'factures' => $this->whenLoaded(
                'factures',
                fn () => FactureResource::collection($this->factures)
            ),
        ];
    }
}