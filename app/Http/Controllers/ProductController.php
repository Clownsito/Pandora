<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CachedProduct;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');

        $products = CachedProduct::query()
            ->when($search, function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return view('products.index', compact('products', 'search'));
    }
}
