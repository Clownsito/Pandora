<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->company_id) {
            abort(403, 'Usuario sin empresa asociada');
        }

        // Guardamos la empresa activa en el request
        $request->attributes->set('company_id', $user->company_id);

        return $next($request);
    }
}
