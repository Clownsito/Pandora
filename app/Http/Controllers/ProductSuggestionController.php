<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Services\SuggestedPriceService;
use Illuminate\Http\JsonResponse;

class ProductSuggestionController extends Controller
{
    public function __invoke(CachedProduct $product): JsonResponse
    {
        return response()->json([
            'suggestions' => app(SuggestedPriceService::class)
                ->getSuggestions($product),
        ]);
    }
}
