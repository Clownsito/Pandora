<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CachedProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $search = $request->get('q');

        $products = CachedProduct::with('latestApprovedPrice')
            ->where('company_id', $companyId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sku', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('products.index', [
            'products' => $products,
            'search'   => $search,
        ]);
    }
}
