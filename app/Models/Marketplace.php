<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marketplace extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'commission_percent',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
