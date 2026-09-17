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
            $routeName = $request->route()?->getName();

            if (in_array($routeName, ['mantenimiento', 'entrega-fest.mantenimiento'], true) || $request->path() === 'up') {
                return $next($request);
            }

            if (str_starts_with((string) $routeName, 'entrega-fest.')) {
                return redirect()->route('entrega-fest.mantenimiento');
            }

            return redirect()->route('mantenimiento');
        }

        return $next($request);
    }
}
