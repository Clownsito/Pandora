<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarginRule extends Model
{
    protected $fillable = [
        'channel',
        'type',
        'margin_percent',
    ];

    protected $casts = [
        'margin_percent' => 'float',
    ];
}
