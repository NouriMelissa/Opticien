<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\RoleEnum;

class Utilisateur extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'tel_portable',
        'role',
        'actif',
        'id_wilaya',
        'created_at',
    ];

    protected $hidden = ['password'];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::ADMIN->value;
    }

    public function isVendeur(): bool
    {
        return $this->role === RoleEnum::VENDEUR->value;
    }
}