<?php

namespace App\Services;

use App\Models\CachedProduct;
use App\Models\MarginRule;

class SuggestedPriceService
{
    protected float $marketplaceCommission = 0.15;

    protected function rule(string $channel, string $type): MarginRule
    {
        return MarginRule::where('channel', $channel)
            ->where('type', $type)
            ->firstOrFail();
    }

    protected function webPrice(float $cost, float $margin): float
    {
        return round($cost / (1 - $margin));
    }

    protected function marketplacePrice(float $cost, float $margin): float
    {
        return round(
            $cost / ((1 - $margin) * (1 - $this->marketplaceCommission))
        );
    }

    public function getSuggestions(CachedProduct $product): array
    {
        $cost = (float) $product->cost;

        $webNormal = $this->rule('web','normal');
        $webOferta = $this->rule('web','oferta');
        $marketNormal = $this->rule('marketplace','normal');
        $marketOferta = $this->rule('marketplace','oferta');

        return [
            'web' => [
                'normal' => [
                    'price'  => $this->webPrice($cost, $webNormal->margin_percent / 100),
                    'margin' => $webNormal->margin_percent
                ],
                'oferta' => [
                    'price'  => $this->webPrice($cost, $webOferta->margin_percent / 100),
                    'margin' => $webOferta->margin_percent
                ],
            ],

            'marketplace' => [
                'normal' => [
                    'price'  => $this->marketplacePrice($cost, $marketNormal->margin_percent / 100),
                    'margin' => $marketNormal->margin_percent
                ],
                'oferta' => [
                    'price'  => $this->marketplacePrice($cost, $marketOferta->margin_percent / 100),
                    'margin' => $marketOferta->margin_percent
                ],
            ],
        ];
    }
}
