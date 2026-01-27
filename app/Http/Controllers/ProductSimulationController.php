<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Models\Marketplace;
use App\Services\ProductSimulationService;
use Illuminate\Http\Request;

class ProductSimulationController extends Controller
{
    public function simulate(
        Request $request,
        CachedProduct $product,
        ProductSimulationService $service
    ) {
        $request->validate([
            'sale_price' => 'required|numeric|min:0',
            'marketplace_id' => 'nullable|exists:marketplaces,id',
        ]);

        $marketplace = null;

        if ($request->marketplace_id) {
            $marketplace = Marketplace::find($request->marketplace_id);
        }

        $result = $service->simulate(
            product: $product,
            salePrice: (float) $request->sale_price,
            marketplace: $marketplace
        );

        return response()->json($result);
    }
}
