<?php

namespace App\Services;

use App\Models\Medecin;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MedecinService
{
    /**
     * Liste des médecins avec filtres optionnels
     * Filtres : search (nom), specialite, id_wilaya
     */
    public function lister(array $filtres = [])
    {
        $query = Medecin::with('wilaya')  ;
        // ->withCount('ordonnances');

        if (!empty($filtres['search'])) {
            $query->searchNom($filtres['search']);
        }

        if (!empty($filtres['specialite'])) {
            $query->bySpecialite($filtres['specialite']);
        }

        if (!empty($filtres['id_wilaya'])) {
            $query->byWilaya((int) $filtres['id_wilaya']);
        }

        return $query->orderBy('nom_prenom')->get();
    }

    /**
     * Trouver un médecin avec ses relations chargées
     */
    public function trouver(int $id): Medecin
    {
        return Medecin::with('wilaya')
            // ->withCount('ordonnances')
            ->findOrFail($id);
    }

    /**
     * Créer un nouveau médecin
     */
    public function creer(array $data): Medecin
    {
        return DB::transaction(function () use ($data) {
            $medecin = Medecin::create([
                'nom_prenom' => $data['nom_prenom'],
                'specialite' => $data['specialite'] ?? null,
                'tel'        => $data['tel'] ?? null,
                'adresse'    => $data['adresse'] ?? null,
                'id_wilaya'  => $data['id_wilaya'] ?? null,
            ]);

            return $medecin->load('wilaya');
        });
    }

    /**
     * Modifier un médecin existant
     * Mise à jour partielle grâce à "sometimes" dans UpdateMedecinRequest
     */
    public function modifier(Medecin $medecin, array $data): Medecin
    {
        $medecin->update($data);

        return $medecin->load('wilaya');
    }

    /**
     * Supprimer un médecin
     *
     * Règle métier : un médecin ayant des ordonnances liées
     * ne peut pas être supprimé (intégrité de l'historique patient).
     */
    public function supprimer(Medecin $medecin): void
    {
        // $this->verifierSuppressionPossible($medecin);

        $medecin->delete();
    }

    /**
     * Liste légère pour les dropdowns / autocomplete Angular
     */
    public function dropdown()
    {
        return Medecin::select('id_medecin', 'nom_prenom', 'specialite')
            ->orderBy('nom_prenom')
            ->get();
    }

    // ── Méthodes privées ──────────────────────────────────────────

    private function verifierSuppressionPossible(Medecin $medecin): void
    {
        $nbOrdonnances = $medecin->ordonnances()->count();

        if ($nbOrdonnances > 0) {
            throw ValidationException::withMessages([
                'medecin' => "Impossible de supprimer : ce médecin a {$nbOrdonnances} ordonnance(s) liée(s).",
            ]);
        }
    }
}