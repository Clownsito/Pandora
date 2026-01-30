<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Determina si el usuario tiene rol de administrador.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return strtolower($this->role) === 'admin';
    }

    /**
     * Relación con la empresa a la que pertenece el usuario.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}


