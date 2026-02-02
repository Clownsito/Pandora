<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarginRule;
use Illuminate\Http\Request;

class MarginRuleController extends Controller
{
    public function index()
    {
        $rules = MarginRule::orderBy('channel')
            ->orderBy('type')
            ->get();

        return view('admin.margins.index', compact('rules'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'margins' => 'required|array',
            'margins.*' => 'numeric|min:0|max:100',
        ]);

        foreach ($request->margins as $id => $value) {
            MarginRule::where('id', $id)
                ->update([
                    'margin_percent' => $value
                ]);
        }

        return back()->with('success', 'Márgenes actualizados correctamente');
    }
}