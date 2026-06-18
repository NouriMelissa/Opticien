<?php

namespace App\Services;

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class UtilisateurService
{
    // LISTE
    public function getAll()
    {
        return Utilisateur::all();
    }

    // DETAIL
    public function getById($id)
    {
        return Utilisateur::findOrFail($id);
    }

    // CREATE
    public function create(array $data)
    {
        return Utilisateur::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tel_portable' => $data['tel_portable'] ?? null,
            'role' => $data['role'],
            'actif' => true,
            'id_wilaya' => $data['id_wilaya'] ?? null,
            'created_at' => now(),
        ]);
    }

    // UPDATE
    public function update($id, array $data)
    {
        $utilisateur = Utilisateur::findOrFail($id);

        $utilisateur->update([
            'nom' => $data['nom'] ?? $utilisateur->nom,
            'prenom' => $data['prenom'] ?? $utilisateur->prenom,
            'email' => $data['email'] ?? $utilisateur->email,
            'tel_portable' => $data['tel_portable'] ?? $utilisateur->tel_portable,
            'role' => $data['role'] ?? $utilisateur->role,
            'actif' => $data['actif'] ?? $utilisateur->actif,
            'id_wilaya' => $data['id_wilaya'] ?? $utilisateur->id_wilaya,
        ]);

        return $utilisateur;
    }

    // DELETE
    public function delete($id)
    {
        $user = Utilisateur::findOrFail($id);

        $user->delete();

        return true;
    }
}