<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Models\Marketplace;
use App\Services\ProductSimulationService;
use App\Services\ApprovePriceService;
use Illuminate\Http\Request;

class ProductApprovalController extends Controller
{
    public function store(
        Request $request,
        CachedProduct $product,
        ProductSimulationService $simulationService,
        ApprovePriceService $approveService
    ) {
        $request->validate([
            'sale_price' => ['required', 'numeric', 'min:0'],
            'marketplace_id' => ['nullable', 'exists:marketplaces,id'],
        ]);

        $marketplace = $request->marketplace_id
            ? Marketplace::find($request->marketplace_id)
            : null;

        $simulation = $simulationService->simulate(
            product: $product,
            salePrice: (float) $request->sale_price,
            marketplace: $marketplace
        );

        $approveService->approve(
            product: $product,
            salePrice: (float) $request->sale_price,
            grossMargin: $simulation['pricing']['gross_margin_percent'],
            realMargin: $simulation['pricing']['real_margin_percent'],
            status: $simulation['final_status'] === 'ok' ? 'ok' : 'rejected',
            marketplace: $marketplace
        );

        return response()->json([
            'message' => 'Precio guardado correctamente',
            'status' => $simulation['final_status'],
        ]);
    }
}
