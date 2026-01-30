<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->company_id) {
            abort(403, 'No tienes una empresa asignada.');
        }

        // Guardamos la empresa activa en el request
        $request->attributes->set('company_id', $request->user()->company_id);

        return $next($request);
    }
}

