<?php

namespace App\Http\Controllers;

use App\Models\SessionShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionShiftController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return SessionShift::with('utilisateur')->get();
        }

        return SessionShift::where('id_util', $user->id)
            ->with('utilisateur')
            ->get();
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        return SessionShift::create([
            'date_ouverture' => now(),
            'fond_caisse_initial' => $request->fond_caisse_initial,
            'statut' => 'OUVERTE',
            'id_util' => $user->id,
            'total_especes' => 0,
            'total_tpe' => 0,
            'total_ventes' => 0,
        ]);
    }
}