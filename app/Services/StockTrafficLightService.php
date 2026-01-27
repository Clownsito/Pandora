<?php

namespace App\Services;

use App\Models\CompanySetting;

class StockTrafficLightService
{
    public function evaluate(int $stock, CompanySetting $settings): string
    {
        if ($stock <= $settings->stock_rojo_max) {
            return 'rojo';
        }

        if ($stock >= $settings->stock_verde_min) {
            return 'verde';
        }

        return 'amarillo';
    }
}
