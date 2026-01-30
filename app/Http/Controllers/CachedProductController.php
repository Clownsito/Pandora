<?php

namespace App\Http\Controllers;

use App\Models\CachedProduct;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CachedProductController extends Controller
{
    /**
     * LISTADO (NO TOCADO)
     */
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

    /**
     * 💡 SUGERENCIAS DE PRECIO (ESTE FALTABA)
     */
    public function suggestions(CachedProduct $product)
    {
        $settings = $product->company->settings;

        $webMargin = $settings->margen_min_percent;
        $mpMargin  = $settings->margen_max_percent;

        $webPrice = round($product->cost * (1 + $webMargin / 100));
        $mpPrice  = round($product->cost * (1 + $mpMargin / 100));

        return response()->json([
            'suggestions' => [
                'web' => [
                    'price' => $webPrice,
                    'margin' => $webMargin,
                ],
                'marketplace' => [
                    'price' => $mpPrice,
                    'margin' => $mpMargin,
                ],
            ],
        ]);
    }

    /**
     * ▶ SIMULACIÓN DE PRECIO
     */
    public function simulate(Request $request, CachedProduct $product)
    {
        $salePrice = (float) $request->sale_price;
        $marketplaceId = $request->marketplace_id;

        $commission = 0;

        if ($marketplaceId) {
            $marketplace = Marketplace::find($marketplaceId);
            $commission = $marketplace?->commission_percent ?? 0;
        }

        $grossMargin = (($salePrice - $product->cost) / $salePrice) * 100;
        $realMargin  = (
            ($salePrice * (1 - $commission / 100) - $product->cost)
            / $salePrice
        ) * 100;

        return response()->json([
            'pricing' => [
                'gross_margin_percent' => round($grossMargin, 2),
                'real_margin_percent'  => round($realMargin, 2),
            ],
            'final_status' => $realMargin >= $product->company->settings->margen_min_percent
                ? 'ok'
                : 'warning',
        ]);
    }

    /**
     * Importar stock vía CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $name = 'inventory_' . date('Y-m-d_H-i') . '.csv';
        $path = $file->storeAs('imports', $name);

        $companyId = Auth::user()->company_id;
        $updated = 0;
        $ignored = 0;
        $errored = false;
        $error_message = null;

        try {
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                throw new \Exception('No se pudo abrir el archivo CSV.');
            }

            // Obtener encabezados y mapear los índices
            $header = fgetcsv($handle, 0, ",");
            if (!$header) {
                throw new \Exception('El archivo CSV está vacío o es inválido.');
            }

            // Normalizar a minúsculas para evitar problemas de mayúsculas/minúsculas
            $columns = array_map(fn($h) => mb_strtolower(trim($h)), $header);
            $skuIdx = array_search('sku', $columns);
            $articuloIdx = array_search('articulo', $columns);
            $stockIdx = array_search('q.saldo', $columns);
            $costIdx = array_search('costo promedio', $columns);

            if ($skuIdx === false || $articuloIdx === false || $stockIdx === false || $costIdx === false) {
                throw new \Exception('El archivo CSV no tiene todas las columnas requeridas (sku, articulo, q.saldo, costo promedio).');
            }

            while (($row = fgetcsv($handle, 0, ",")) !== false) {
                $sku = trim($row[$skuIdx] ?? '');
                if ($sku === '') {
                    $ignored++;
                    continue;
                }
                $product = \App\Models\CachedProduct::where('company_id', $companyId)->where('sku', $sku)->first();
                if ($product) {
                    $product->name = (string)($row[$articuloIdx] ?? $product->name);
                    $product->stock = (int)($row[$stockIdx] ?? $product->stock);
                    $product->cost = is_numeric($row[$costIdx] ?? null) ? floatval($row[$costIdx]) : $product->cost;
                    $product->save();
                    $updated++;
                } else {
                    $ignored++;
                }
            }
            fclose($handle);
        } catch (\Exception $e) {
            $errored = true;
            $error_message = $e->getMessage();
        }

        if ($errored) {
            return redirect()->route('products.index')
                ->with('error', 'Error al importar el CSV: ' . $error_message);
        }

        return redirect()->route('products.index')
            ->with('success', "Archivo importado correctamente. Productos actualizados: {$updated}. Filas ignoradas: {$ignored}.");
    }

    /**
     * Importar stock automáticamente desde archivo inventory_current.csv
     */
    public function importBotFile(Request $request)
    {
        $filePath = 'C:/data/pandora/inventory/inventory_current.csv';

        // Crear carpeta si no existe
        $directory = dirname($filePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!file_exists($filePath)) {
            return redirect()->route('products.index')
                ->with('error', 'No se encontró el archivo inventory_current.csv en: ' . $filePath);
        }

        $companyId = Auth::user()->company_id;
        $updated = 0;
        $ignored = 0;
        $errored = false;
        $error_message = null;

        $name = 'inventory_' . date('Y-m-d_H-i') . '.csv';
        $storagePath = storage_path('app/imports');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        copy($filePath, $storagePath . '/' . $name);

        try {
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                throw new \Exception('No se pudo abrir el archivo CSV.');
            }
            $header = fgetcsv($handle, 0, ",");
            if (!$header) {
                throw new \Exception('El archivo CSV está vacío o es inválido.');
            }
            $columns = array_map(fn($h) => mb_strtolower(trim($h)), $header);
            $skuIdx = array_search('sku', $columns);
            $articuloIdx = array_search('articulo', $columns);
            $stockIdx = array_search('q.saldo', $columns);
            $costIdx = array_search('costo promedio', $columns);
            if ($skuIdx === false || $articuloIdx === false || $stockIdx === false || $costIdx === false) {
                throw new \Exception('El archivo CSV no tiene todas las columnas requeridas (sku, articulo, q.saldo, costo promedio).');
            }
            while (($row = fgetcsv($handle, 0, ",")) !== false) {
                $sku = trim($row[$skuIdx] ?? '');
                if ($sku === '') {
                    $ignored++;
                    continue;
                }
                $product = \App\Models\CachedProduct::where('company_id', $companyId)->where('sku', $sku)->first();
                if ($product) {
                    $product->name = (string)($row[$articuloIdx] ?? $product->name);
                    $product->stock = (int)($row[$stockIdx] ?? $product->stock);
                    $product->cost = is_numeric($row[$costIdx] ?? null) ? floatval($row[$costIdx]) : $product->cost;
                    $product->save();
                    $updated++;
                } else {
                    $ignored++;
                }
            }
            fclose($handle);
        } catch (\Exception $e) {
            $errored = true;
            $error_message = $e->getMessage();
        }

        if ($errored) {
            return redirect()->route('products.index')
                ->with('error', 'Error al importar inventory_current.csv: ' . $error_message);
        }

        return redirect()->route('products.index')
            ->with('success', "Archivo automático importado correctamente. Productos actualizados: {$updated}. Filas ignoradas: {$ignored}.");
    }
}
