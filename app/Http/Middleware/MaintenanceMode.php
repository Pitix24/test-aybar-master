<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.maintenance.active', false)) {
            if ($request->path() === 'mantenimiento' || $request->path() === 'up') {
                return $next($request);
            }

            return redirect()->route('mantenimiento');
        }

        return $next($request);
    }
}
