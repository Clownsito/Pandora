<?php

namespace App\Services;

use App\Models\CachedProduct;
use App\Models\Marketplace;

class ProductSimulationService
{
    protected MarginCalculatorService $marginCalculator;
    protected StockTrafficLightService $stockTrafficLight;
    protected MarginPolicyService $marginPolicy;

    public function __construct(
        MarginCalculatorService $marginCalculator,
        StockTrafficLightService $stockTrafficLight,
        MarginPolicyService $marginPolicy
    ) {
        $this->marginCalculator = $marginCalculator;
        $this->stockTrafficLight = $stockTrafficLight;
        $this->marginPolicy = $marginPolicy;
    }

    public function simulate(
        CachedProduct $product,
        float $salePrice,
        ?Marketplace $marketplace = null
    ): array {
        $settings = $product->company->settings;

        // 1. Margen
        $marginData = $this->marginCalculator->calculate(
            cost: (float) $product->cost,
            salePrice: $salePrice,
            commissionPercent: $marketplace?->commission_percent
        );

        // 2. Semáforo de stock
        $stockStatus = $this->stockTrafficLight->evaluate(
            $product->stock,
            $settings
        );

        // 3. Política de margen
        $policyResult = $this->marginPolicy->validate(
            $marginData['real_margin_percent'],
            $settings
        );

        return [
            'product' => [
                'sku' => $product->sku,
                'name' => $product->name,
                'stock' => $product->stock,
                'stock_status' => $stockStatus,
            ],
            'pricing' => $marginData,
            'policy' => $policyResult,
            'final_status' => $policyResult['status'],
        ];
    }
}
