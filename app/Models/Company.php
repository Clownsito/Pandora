<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function settings()
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function marketplaces()
    {
        return $this->hasMany(Marketplace::class);
    }

    public function products()
    {
        return $this->hasMany(CachedProduct::class);
    }
}
