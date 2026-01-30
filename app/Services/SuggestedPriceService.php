<?php

namespace App\Services;

use App\Models\CachedProduct;

class SuggestedPriceService
{
    public function getSuggestions(CachedProduct $product): array
    {
        $cost = (float) $product->cost;

        $webMargin = 0.30;
        $marketMargin = 0.40;

        return [
            'web' => [
                'price'  => round($cost / (1 - $webMargin)),
                'margin' => 30,
            ],
            'marketplace' => [
                'price'  => round($cost / (1 - $marketMargin)),
                'margin' => 40,
            ],
        ];
    }
}
