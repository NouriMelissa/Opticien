<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Http\Requests\MedecinRequest;
use App\Http\Resources\MedecinResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * MedecinController
 *
 * Gère toutes les opérations CRUD sur les médecins.
 * Répond uniquement en JSON (API REST pour Angular).
 *
 * Routes couvertes :
 *   GET    /api/medecins           → index()   : liste paginée + filtres
 *   POST   /api/medecins           → store()   : créer un médecin
 *   GET    /api/medecins/{id}      → show()    : détail d'un médecin
 *   PUT    /api/medecins/{id}      → update()  : modifier un médecin
 *   DELETE /api/medecins/{id}      → destroy() : supprimer un médecin
 */
class MedecinController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // INDEX — Liste de tous les médecins (avec filtres et pagination)
    // GET /api/medecins?search=bensalem&specialite=Ophtalmo&wilaya=16&per_page=15
    // ════════════════════════════════════════════════════════════════
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Medecin::with('wilaya')         // Charger la wilaya liée
                        ->withCount('ordonnances'); // Compter les ordonnances

        // ── Filtre : recherche par nom ──────────────────────────────
        if ($request->filled('search')) {
            $query->searchNom($request->search);
        }

        // ── Filtre : par spécialité ─────────────────────────────────
        if ($request->filled('specialite')) {
            $query->bySpecialite($request->specialite);
        }

        // ── Filtre : par wilaya ─────────────────────────────────────
        if ($request->filled('wilaya')) {
            $query->byWilaya((int) $request->wilaya);
        }

        // ── Tri ─────────────────────────────────────────────────────
        $query->orderBy('nom_prenom', 'asc');

        // ── Pagination : 15 par défaut, max 100 ────────────────────
        $perPage = min((int) $request->get('per_page', 15), 100);

        $medecins = $query->paginate($perPage);

        return MedecinResource::collection($medecins);
        // ↑ Retourne automatiquement :
        // { data: [...], links: {...}, meta: { total, current_page, ... } }
    }

    // ════════════════════════════════════════════════════════════════
    // STORE — Créer un nouveau médecin
    // POST /api/medecins
    // Body JSON : { nom_prenom, specialite?, tel?, adresse?, id_wilaya? }
    // ════════════════════════════════════════════════════════════════
    public function store(MedecinRequest $request): JsonResponse
    {
        // $request->validated() = seulement les champs validés (sécurisé)
        $medecin = Medecin::create($request->validated());

        // Charger la wilaya pour l'inclure dans la réponse
        $medecin->load('wilaya');

        return response()->json([
            'success' => true,
            'message' => 'Médecin créé avec succès.',
            'data'    => new MedecinResource($medecin),
        ], 201); // 201 Created
    }

    // ════════════════════════════════════════════════════════════════
    // SHOW — Détail d'un médecin par son ID
    // GET /api/medecins/{id_medecin}
    // ════════════════════════════════════════════════════════════════
    public function show(int $id): JsonResponse
    {
        // findOrFail → 404 automatique si non trouvé
        $medecin = Medecin::with('wilaya')
                          ->withCount('ordonnances')
                          ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => new MedecinResource($medecin),
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // UPDATE — Modifier un médecin existant
    // PUT /api/medecins/{id_medecin}
    // Body JSON : { nom_prenom?, specialite?, tel?, adresse?, id_wilaya? }
    // ════════════════════════════════════════════════════════════════
    public function update(MedecinRequest $request, int $id): JsonResponse
    {
        $medecin = Medecin::findOrFail($id);

        // update() avec les données validées uniquement
        $medecin->update($request->validated());

        // Recharger avec les relations pour la réponse
        $medecin->load('wilaya');

        return response()->json([
            'success' => true,
            'message' => 'Médecin mis à jour avec succès.',
            'data'    => new MedecinResource($medecin),
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // DESTROY — Supprimer un médecin
    // DELETE /api/medecins/{id_medecin}
    // ════════════════════════════════════════════════════════════════
    public function destroy(int $id): JsonResponse
    {
        $medecin = Medecin::findOrFail($id);

        // ── Vérification : ce médecin a-t-il des ordonnances liées ? ─
        $nbOrdonnances = $medecin->ordonnances()->count();

        if ($nbOrdonnances > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : ce médecin a {$nbOrdonnances} ordonnance(s) liée(s).",
            ], 409); // 409 Conflict
        }

        $medecin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Médecin supprimé avec succès.',
        ], 200);
    }

    // ════════════════════════════════════════════════════════════════
    // LISTE RAPIDE — Pour les dropdowns dans Angular (sans pagination)
    // GET /api/medecins/dropdown
    // Retourne uniquement id + nom pour les select/autocomplete
    // ════════════════════════════════════════════════════════════════
    public function dropdown(): JsonResponse
    {
        $medecins = Medecin::select('id_medecin', 'nom_prenom', 'specialite')
                           ->orderBy('nom_prenom')
                           ->get();

        return response()->json([
            'success' => true,
            'data'    => $medecins,
        ]);
    }
}