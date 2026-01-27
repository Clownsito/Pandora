<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CachedProduct;
use App\Models\Company;

class CachedProductSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        CachedProduct::create([
            'company_id' => $company->id,
            'sku' => 'SKU-TEST-001',
            'name' => 'Producto de Prueba',
            'cost' => 15000,
            'stock' => 8,
            'last_sync_at' => now(),
        ]);
    }
}
