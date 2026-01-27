<?php

namespace App\Services;

use App\Models\CompanySetting;

class MarginPolicyService
{
    public function validate(
        float $realMarginPercent,
        CompanySetting $settings
    ): array {
        if ($realMarginPercent < $settings->margen_min_percent) {
            return [
                'status' => 'INVALID',
                'message' => 'Margen por debajo del mínimo permitido',
            ];
        }

        if ($realMarginPercent > $settings->margen_max_percent) {
            return [
                'status' => 'INVALID',
                'message' => 'Margen por encima del máximo permitido',
            ];
        }

        if ($realMarginPercent <= ($settings->margen_min_percent + 5)) {
            return [
                'status' => 'WARNING',
                'message' => 'Margen cercano al mínimo permitido',
            ];
        }

        return [
            'status' => 'VALID',
            'message' => 'Margen dentro de los parámetros',
        ];
    }
}
