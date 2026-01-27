<?php

namespace App\Services;

use App\Models\CachedProduct;

class SuggestedPriceService
{
    public function getSuggestions(CachedProduct $product): array
    {
        $cost = (float) $product->cost;

        // Reglas simples (después las refinamos)
        $webMargin = 0.30;          // 30%
        $marketMargin = 0.40;       // 40%

        $webPrice = round($cost / (1 - $webMargin));
        $marketplacePrice = round($cost / (1 - $marketMargin));

        return [
            'web' => [
                'price'  => $webPrice,
                'margin' => $webMargin * 100,
            ],
            'marketplace' => [
                'price'  => $marketplacePrice,
                'margin' => $marketMargin * 100,
            ],
        ];
    }
}
