<?php

namespace App\Services;

use App\Models\ApprovedPrice;
use App\Models\CachedProduct;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApprovePriceService
{
    public function approve(
        CachedProduct $product,
        float $salePrice,
        float $grossMargin,
        float $realMargin,
        string $status,
        ?Marketplace $marketplace = null
    ): ApprovedPrice {
        return ApprovedPrice::updateOrCreate(
            [
                'cached_product_id' => $product->id,
                'marketplace_id' => $marketplace?->id,
            ],
            [
                'company_id' => $product->company_id,
                'sale_price' => $salePrice,
                'gross_margin_percent' => $grossMargin,
                'real_margin_percent' => $realMargin,
                'status' => $status,
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]
        );
    }
}
