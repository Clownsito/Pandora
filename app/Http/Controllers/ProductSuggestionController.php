<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Services\SuggestedPriceService;
use Illuminate\Http\JsonResponse;

class ProductSuggestionController extends Controller
{
    public function __invoke(CachedProduct $product): JsonResponse
    {
        $service = app(SuggestedPriceService::class);

        $suggestions = $service->getSuggestions($product);

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }
}
