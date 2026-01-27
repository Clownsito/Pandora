<?php

namespace App\Services;

class MarginCalculatorService
{
    /**
     * Calcula márgenes según canal
     */
    public function calculate(
        float $cost,
        float $salePrice,
        ?float $commissionPercent = null
    ): array {
        if ($salePrice <= 0) {
            throw new \InvalidArgumentException('El precio de venta debe ser mayor a 0');
        }

        if ($cost < 0) {
            throw new \InvalidArgumentException('El costo no puede ser negativo');
        }

        // Margen en dinero
        $marginAmount = $salePrice - $cost;

        // Margen bruto %
        $grossMarginPercent = ($marginAmount / $salePrice) * 100;

        // Margen real %
        $realMarginPercent = $grossMarginPercent;

        if (!is_null($commissionPercent)) {
            $realMarginPercent = $grossMarginPercent - $commissionPercent;
        }

        return [
            'sale_price' => round($salePrice, 2),
            'cost' => round($cost, 2),

            'margin_amount' => round($marginAmount, 2),

            'gross_margin_percent' => round($grossMarginPercent, 2),
            'real_margin_percent' => round($realMarginPercent, 2),

            'commission_percent' => $commissionPercent,
        ];
    }
}
