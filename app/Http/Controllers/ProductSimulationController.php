<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Models\Marketplace;
use App\Models\MarginRule;
use App\Services\ProductSimulationService;
use App\Services\SuggestedPriceService;
use Illuminate\Http\Request;

class ProductSimulationController extends Controller
{
    public function showSimulation(
        CachedProduct $product,
        SuggestedPriceService $suggestedPriceService
    ) {
        // Precios sugeridos (ya usan los márgenes correctos)
        $suggestions = $suggestedPriceService->getSuggestions($product);

        // Márgenes dinámicos desde BD
        $margins = MarginRule::all()
            ->mapWithKeys(fn ($rule) => [
                "{$rule->channel}_{$rule->type}" => $rule->margin_percent
            ]);

        return view('products.simulate', [
            'product' => $product,
            'suggestions' => $suggestions,
            'margins' => $margins,
        ]);
    }

    public function simulate(
        Request $request,
        CachedProduct $product,
        ProductSimulationService $service
    ) {
        $request->validate([
            'sale_price' => 'required|numeric|min:0',
            'marketplace_id' => 'nullable|exists:marketplaces,id'
        ]);

        $marketplace = null;

        if ($request->marketplace_id) {
            $marketplace = Marketplace::where('id', $request->marketplace_id)
                ->where('company_id', $product->company_id)
                ->first();
        }

        $result = $service->simulate(
            product: $product,
            salePrice: (float) $request->sale_price,
            marketplace: $marketplace
        );

        return response()->json($result);
    }
}
