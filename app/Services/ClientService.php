<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientService
{
    /**
     * Liste des clients avec filtres optionnels
     * Filtres : search (nom), id_wilaya, tel
     */
    public function lister(array $filtres = [])
    {
        $query = Client::with('wilaya')
                       ->withCount(['ventes', 'ordonnances']);

        if (!empty($filtres['search'])) {
            $query->searchNom($filtres['search']);
        }

        if (!empty($filtres['id_wilaya'])) {
            $query->byWilaya((int) $filtres['id_wilaya']);
        }

        if (!empty($filtres['tel'])) {
            $query->byTel($filtres['tel']);
        }

        return $query->orderByDesc('client_depuis')
                     ->paginate($filtres['per_page'] ?? 15);
    }

    public function trouver(int $id): Client
    {
        return Client::with(['wilaya', 'createur'])
            ->withCount(['ventes', 'ordonnances'])
            ->findOrFail($id);
    }

    /**
     * Créer un client.
     * client_depuis est TOUJOURS fixé à aujourd'hui automatiquement,
     * jamais envoyé par le frontend.
     */
    public function creer(array $data, ?int $idUtilCreateur = null): Client
    {
        return DB::transaction(function () use ($data, $idUtilCreateur) {
            $client = Client::create([
                'nom_prenom'      => $data['nom_prenom'],
                'date_naissance'  => $data['date_naissance'] ?? null,
                'tel_portable'    => $data['tel_portable'] ?? null,
                'tel_fixe'        => $data['tel_fixe'] ?? null,
                'email'           => $data['email'] ?? null,
                'id_wilaya'       => $data['id_wilaya'] ?? null,
                'commune'         => $data['commune'] ?? null,
                'adresse'         => $data['adresse'] ?? null,
                'profession'      => $data['profession'] ?? null,
                'remarque'        => $data['remarque'] ?? null,
                'client_depuis'   => now()->toDateString(),   // ← auto, jamais saisi
                'derniere_visite' => null,                    // ← rempli à la 1ère vente
                'created_by'      => $idUtilCreateur ?? $data['created_by'] ?? null,
            ]);

            return $client->load('wilaya');
        });
    }

    /**
     * Modifier un client.
     * client_depuis et derniere_visite ne sont jamais touchés ici
     * (déjà exclus par UpdateClientRequest, double sécurité).
     */
    public function modifier(Client $client, array $data): Client
    {
        unset($data['client_depuis'], $data['derniere_visite'], $data['created_by']);

        $client->update($data);

        return $client->load('wilaya');
    }

    /**
     * Supprimer un client.
     * Bloqué si des ventes ou ordonnances existent (intégrité historique).
     */
    public function supprimer(Client $client): void
    {
        $this->verifierSuppressionPossible($client);

        $client->delete();
    }

    /**
     * Appelée par VenteService au moment de la création d'une vente
     * pour mettre à jour la date de dernière visite.
     */
    public function marquerVisite(int $idClient): void
    {
        Client::where('id_client', $idClient)->update([
            'derniere_visite' => now(),
        ]);
    }

    /**
     * Liste légère pour dropdown / autocomplete Angular
     */
    public function dropdown(string $search = '')
    {
        $query = Client::select('id_client', 'nom_prenom', 'tel_portable');

        if ($search !== '') {
            $query->searchNom($search);
        }

        return $query->orderBy('nom_prenom')->limit(20)->get();
    }

    // ── Privé ────────────────────────────────────────────────────

    private function verifierSuppressionPossible(Client $client): void
    {
        $nbVentes = $client->ventes()->count();
        $nbOrdonnances = $client->ordonnances()->count();

        if ($nbVentes > 0 || $nbOrdonnances > 0) {
            throw ValidationException::withMessages([
                'client' => "Impossible de supprimer : ce client a {$nbVentes} vente(s) et {$nbOrdonnances} ordonnance(s) liée(s).",
            ]);
        }
    }
}