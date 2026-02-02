<?php

namespace App\Services;

class MarginCalculatorService
{
    public function calculate(
        float $cost,
        float $salePrice,
        float $commissionPercent = 0
    ): array {

        if ($salePrice <= 0) {
            throw new \InvalidArgumentException('Precio inválido');
        }

        // Margen bruto
        $grossProfit = $salePrice - $cost;
        $grossMarginPercent = ($grossProfit / $salePrice) * 100;

        // Comisión marketplace
        $commissionAmount = $salePrice * ($commissionPercent / 100);

        // Margen real
        $realRevenue = $salePrice - $commissionAmount;
        $realProfit = $realRevenue - $cost;
        $realMarginPercent = ($realProfit / $salePrice) * 100;

        return [
            'sale_price' => round($salePrice, 2),
            'cost' => round($cost, 2),

            'commission_percent' => round($commissionPercent, 2),
            'commission_amount' => round($commissionAmount, 2),

            'gross_profit' => round($grossProfit, 2),
            'gross_margin_percent' => round($grossMarginPercent, 2),

            'real_revenue' => round($realRevenue, 2),
            'real_profit' => round($realProfit, 2),
            'real_margin_percent' => round($realMarginPercent, 2),
        ];
    }
}
