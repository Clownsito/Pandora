<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'stock_rojo_max',
        'stock_amarillo_min',
        'stock_verde_min',
        'margen_min_percent',
        'margen_max_percent',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
