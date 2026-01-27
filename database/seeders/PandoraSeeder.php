<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\CompanySetting;
use App\Models\Marketplace;

class PandoraSeeder extends Seeder
{
    public function run(): void
    {
        // Empresa demo
        $company = Company::create([
            'name' => 'Empresa Demo',
        ]);

        // Usuario ADMIN
        User::create([
            'company_id' => $company->id,
            'name' => 'Admin Pandora',
            'email' => 'admin@pandora.local',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);

        // Configuración de empresa
        CompanySetting::create([
            'company_id' => $company->id,

            // Stock semáforo
            'stock_rojo_max' => 3,
            'stock_amarillo_min' => 4,
            'stock_verde_min' => 10,

            // Márgenes
            'margen_min_percent' => 10,
            'margen_max_percent' => 50,
        ]);

        // Marketplace default
        Marketplace::create([
            'company_id' => $company->id,
            'name' => 'Marketplace General',
            'commission_percent' => 15,
        ]);
    }
}
