<?php

namespace App\Services;

use App\Models\Magasin;

class MagasinService
{
    public function getAll()
    {
        return Magasin::orderBy('id_magasin')->get();
    }

    public function getById($id)
    {
        return Magasin::findOrFail($id);
    }

    public function create(array $data)
    {
        return Magasin::create($data);
    }

    public function update($id, array $data)
    {
        $magasin = Magasin::findOrFail($id);

        $magasin->update($data);

        return $magasin;
    }

    public function delete($id)
    {
        $magasin = Magasin::findOrFail($id);

        $magasin->delete();

        return true;
    }
}