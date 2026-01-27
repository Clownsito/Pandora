<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovedPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'cached_product_id',
        'marketplace_id',
        'sale_price',
        'gross_margin_percent',
        'real_margin_percent',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(CachedProduct::class, 'cached_product_id');
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
