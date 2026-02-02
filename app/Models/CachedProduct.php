<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CachedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'sku',
        'name',
        'cost',
        'stock',
        'last_sync_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedPrices()
    {
        return $this->hasMany(ApprovedPrice::class);
    }

    public function latestApprovedPrice()
    {
        return $this->hasOne(ApprovedPrice::class)->latestOfMany();
    }

    public function productStrategy(): HasOne
    {
        return $this->hasOne(ProductStrategy::class, 'product_id')->where('type', 'featured');
    }
}

