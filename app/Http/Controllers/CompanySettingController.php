<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $companyId = Auth::user()->company_id;

        $settings = CompanySetting::firstOrCreate(
            ['company_id' => $companyId],
            [
                'stock_rojo_max' => 3,
                'stock_amarillo_min' => 4,
                'stock_verde_min' => 10,
                'margen_min_percent' => 5,
                'margen_max_percent' => 50,
            ]
        );

        return view('company-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stock_rojo_max' => 'required|integer|min:0',
            'stock_amarillo_min' => 'required|integer|min:0',
            'stock_verde_min' => 'required|integer|min:0',
            'margen_min_percent' => 'required|numeric|min:0|max:100',
            'margen_max_percent' => 'required|numeric|min:0|max:100',
        ]);

        if ($request->margen_min_percent > $request->margen_max_percent) {
            return back()->withErrors([
                'margen_min_percent' => 'El margen mínimo no puede ser mayor al máximo',
            ]);
        }

        $settings = CompanySetting::where('company_id', Auth::user()->company_id)->firstOrFail();

        $settings->update($request->all());<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Auth;

class CompanySettingController extends Controller
{
    public function edit()
    {
        $companyId = Auth::user()->company_id;
        $settings = CompanySetting::firstOrCreate(['company_id' => $companyId]);
        return view('company-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $settings = CompanySetting::firstOrCreate(['company_id' => $companyId]);

        $request->validate([
            'margin_web_standard' => 'required|numeric|min:0|max:100',
            'margin_marketplace_diff' => 'required|numeric|min:0|max:100',
        ]);

        $settings->update($request->only([
            'margin_web_standard',
            'margin_marketplace_diff',
        ]));

        return redirect()->route('company-settings.edit')
                         ->with('success', 'Configuraci�f3n actualizada correctamente.');
    }
}


        return redirect()
            ->route('company-settings.edit')
            ->with('success', 'Configuración guardada correctamente');
    }
}

