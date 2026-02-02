<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Models\ProductStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CachedProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $search = $request->get('q');
        $featured = $request->get('featured');

        $products = CachedProduct::with('latestApprovedPrice', 'productStrategy')
            ->where('company_id', $companyId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sku', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when($featured, function ($query) use ($companyId) {
                $query->whereHas('productStrategy', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId)
                      ->where('type', 'featured');
                });
            })
            ->orderBy('name')
            ->get();

        $featuredProducts = CachedProduct::with('latestApprovedPrice', 'productStrategy')
            ->where('company_id', $companyId)
            ->whereHas('productStrategy', function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                  ->where('type', 'featured');
            })
            ->orderByDesc(
                ProductStrategy::select('priority')
                    ->whereColumn('cached_products.id', 'product_strategies.product_id')
                    ->where('type', 'featured')
            )
            ->get();

        return view('products.index', [
            'products' => $products,
            'featuredProducts' => $featuredProducts,
            'search' => $search,
            'featured' => $featured,
        ]);
    }

    // ===========================
    // IMPORT CSV KAME
    // ===========================

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $companyId = Auth::user()->company_id;

        $updated = 0;
        $ignored = 0;

        try {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle, 0, ",");

            $columns = array_map(fn($h) => mb_strtolower(trim($h)), $header);
            $skuIdx = array_search('sku', $columns);
            $articuloIdx = array_search('articulo', $columns);
            $stockIdx = array_search('q.saldo', $columns);
            $costIdx = array_search('costo promedio', $columns);

            while (($row = fgetcsv($handle, 0, ",")) !== false) {

                $sku = trim($row[$skuIdx] ?? '');
                if ($sku === '') {
                    $ignored++;
                    continue;
                }

                $product = CachedProduct::where('company_id', $companyId)
                    ->where('sku', $sku)
                    ->first();

                if ($product) {
                    $product->name  = $row[$articuloIdx] ?? $product->name;
                    $product->stock = (int) $row[$stockIdx];
                    $product->cost  = is_numeric($row[$costIdx]) ? floatval($row[$costIdx]) : $product->cost;
                    $product->save();
                    $updated++;
                } else {
                    $ignored++;
                }
            }

            fclose($handle);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Actualizados: $updated | Ignorados: $ignored");
    }

    // ===========================
    // IMPORT AUTOMÁTICO KAME
    // ===========================

    public function importBotFile(Request $request)
    {
        $filePath = 'C:/data/pandora/inventory/inventory_current.csv';

        if (!file_exists($filePath)) {
            return back()->with('error', 'No se encontró inventory_current.csv');
        }

        $companyId = Auth::user()->company_id;
        $updated = 0;
        $ignored = 0;

        try {
            $handle = fopen($filePath, 'r');
            $header = fgetcsv($handle, 0, ",");

            $columns = array_map(fn($h) => mb_strtolower(trim($h)), $header);
            $skuIdx = array_search('sku', $columns);
            $articuloIdx = array_search('articulo', $columns);
            $stockIdx = array_search('q.saldo', $columns);
            $costIdx = array_search('costo promedio', $columns);

            while (($row = fgetcsv($handle, 0, ",")) !== false) {

                $sku = trim($row[$skuIdx] ?? '');
                if ($sku === '') {
                    $ignored++;
                    continue;
                }

                $product = CachedProduct::where('company_id', $companyId)
                    ->where('sku', $sku)
                    ->first();

                if ($product) {
                    $product->name  = $row[$articuloIdx] ?? $product->name;
                    $product->stock = (int) $row[$stockIdx];
                    $product->cost  = is_numeric($row[$costIdx]) ? floatval($row[$costIdx]) : $product->cost;
                    $product->save();
                    $updated++;
                } else {
                    $ignored++;
                }
            }

            fclose($handle);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Auto actualizado: $updated | Ignorados: $ignored");
    }

    // ===========================
    // ⭐ IMPORT CSV ESTRATÉGICOS
    // ===========================

    public function importStrategic(Request $request)
    {
        $request->validate([
            'strategic_csv' => 'required|file|mimes:csv,txt',
        ]);

        $companyId = Auth::user()->company_id;
        $added = 0;
        $ignored = 0;

        $handle = fopen($request->file('strategic_csv')->getRealPath(), 'r');

        $firstRow = fgetcsv($handle);
        $hasHeader = in_array('sku', array_map('strtolower', $firstRow ?? []));

        if (!$hasHeader) {
            rewind($handle);
        }

        while (($row = fgetcsv($handle)) !== false) {

            $sku = trim($row[0] ?? '');
            if ($sku === '') {
                $ignored++;
                continue;
            }

            $product = CachedProduct::where('company_id', $companyId)
                ->where('sku', $sku)
                ->first();

            if (!$product) {
                $ignored++;
                continue;
            }

            $exists = $product->productStrategy()
                ->where('type', 'featured')
                ->exists();

            if (!$exists) {
                $product->productStrategy()->create([
                    'company_id' => $companyId,
                    'type' => 'featured',
                    'priority' => 50,
                    'created_by' => Auth::id(),
                ]);
                $added++;
            }
        }

        fclose($handle);

        return back()->with('success', "Estratégicos añadidos: $added | Ignorados: $ignored");
    }
}
