<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $table = 'factures';
    protected $primaryKey = 'id_facture';
    public $timestamps = false;

    protected $fillable = [
        'numero_facture', 'type_document', 'date_emission', 'date_validite',
        'pdf_path', 'id_vente',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class, 'id_vente', 'id_vente');
    }
}
