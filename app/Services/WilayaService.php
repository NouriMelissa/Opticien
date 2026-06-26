<?php

namespace App\Services;

use App\Models\Wilaya;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WilayaService
{
    /**
     * Liste des wilayas, triée par code (01 → 58)
     * Filtre optionnel : search (nom)
     */
    public function lister(array $filtres = [])
    {
        // $query = Wilaya::withCount(['medecins', 'clients', 'fournisseurs', 'communes']);
        $query = Wilaya::withCount(['medecins']);

        if (!empty($filtres['search'])) {
            $query->searchNom($filtres['search']);
        }

        return $query->orderBy('code')->get();
    }

    public function trouver(int $id): Wilaya
    {
        return Wilaya::withCount(['medecins', 'clients', 'fournisseurs', 'communes'])
            ->findOrFail($id);
    }

    // public function creer(array $data): Wilaya
    // {
    //     return DB::transaction(function () use ($data) {
    //         return Wilaya::create([
    //             'code'       => $data['code'],
    //             'nom_wilaya' => $data['nom_wilaya'],
    //         ]);
    //     });
    // }
    public function creer(array $data): Wilaya
{
    return Wilaya::create($data);
}

    public function modifier(Wilaya $wilaya, array $data): Wilaya
    {
        $wilaya->update($data);

        return $wilaya;
    }

    /**
     * Suppression bloquée si la wilaya est référencée
     * par des médecins, clients, fournisseurs ou communes.
     */
public function supprimer(Wilaya $wilaya): void
{
    $this->verifierSuppressionPossible($wilaya);
    $wilaya->delete();
}
    /**
     * Liste légère pour dropdown Angular (58 wilayas)
     */
    public function dropdown()
    {
        return Wilaya::select('id_wilaya', 'code', 'nom_wilaya')
            ->orderBy('code')
            ->get();
    }

    // ── Privé ────────────────────────────────────────────────────

    private function verifierSuppressionPossible(Wilaya $wilaya): void
    {
        $liens = [
            'médecin(s)'     => $wilaya->medecins()->count(),
            // 'client(s)'      => $wilaya->clients()->count(),
            // 'fournisseur(s)' => $wilaya->fournisseurs()->count(),
            // 'commune(s)'     => $wilaya->communes()->count(),
        ];

        $messages = [];
        foreach ($liens as $label => $count) {
            if ($count > 0) {
                $messages[] = "{$count} {$label}";
            }
        }

        if (!empty($messages)) {
            throw ValidationException::withMessages([
                'wilaya' => 'Impossible de supprimer : cette wilaya est liée à ' . implode(', ', $messages) . '.',
            ]);
        }
    }
}