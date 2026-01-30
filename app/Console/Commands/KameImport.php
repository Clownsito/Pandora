<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\CachedProduct;

class KameImport extends Command
{
    protected $signature = 'kame:import {company}';
    protected $description = 'Importa inventario KAME desde CSV (final correcto)';

    public function handle()
    {
        $companyName = $this->argument('company');

        $company = Company::where('name', $companyName)->first();
        if (!$company) {
            $this->error("Empresa '{$companyName}' no encontrada");
            return Command::FAILURE;
        }

        $path = storage_path("app/kame/{$companyName}.csv");
        if (!file_exists($path)) {
            $this->error("Archivo no encontrado: {$path}");
            return Command::FAILURE;
        }

        $this->info("Importando inventario para {$company->name}...");

        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->error("No se pudo abrir el CSV");
            return Command::FAILURE;
        }

        // Detectar separador automáticamente
        $firstLine = fgets($handle);
        $separator = str_contains($firstLine, ';') ? ';' : ',';
        rewind($handle);

        $headers = null;
        $count = 0;

        while (($row = fgetcsv($handle, 0, $separator)) !== false) {

            $row = array_map('trim', $row);

            // Detectar fila de encabezados reales
            if ($headers === null) {
                $normalized = array_map(function ($h) {
                    $h = mb_strtolower($h);
                    $h = str_replace(['.', ' '], '_', $h);
                    return $h;
                }, $row);

                if (in_array('sku', $normalized)) {
                    $headers = $normalized;
                }
                continue;
            }

            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            if (empty($data['sku'])) {
                continue;
            }

            CachedProduct::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'sku' => $data['sku'],
                ],
                [
                    // columnas correctas y verificadas
                    'name'  => $data['artículo'] ?? 'Sin nombre',
                    'stock' => (int) ($data['q__saldo_consolidado'] ?? 0),
                    'cost'  => (float) str_replace(',', '.', $data['costo_promedio'] ?? 0),
                ]
            );

            $count++;
        }

        fclose($handle);

        $this->info("Productos importados/actualizados: {$count}");

        return Command::SUCCESS;
    }
}
