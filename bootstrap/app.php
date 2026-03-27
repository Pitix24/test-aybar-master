<?php

use App\Http\Middleware\RedirectIfAuthenticatedByRole;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckCliente;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['web', 'auth', 'check.admin'])
                ->prefix('erp')
                ->name('erp.')
                ->group(function () {
                    foreach (glob(base_path('routes/erp/*.php')) as $file) {
                        require $file;
                    }
                });

            Route::middleware(['web', 'verified', 'auth', 'check.cliente'])
                ->prefix('cliente')
                ->name('cliente.')
                ->group(base_path('routes/cliente.php'));

            Route::middleware('web')
                ->group(function () {
                    foreach (glob(base_path('routes/web/*.php')) as $file) {
                        require $file;
                    }
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'redirect.by.role' => RedirectIfAuthenticatedByRole::class,

            'check.admin' => CheckAdmin::class,
            'check.cliente' => CheckCliente::class,

            // AGREGAR MIDDLEWARE DE SPATIE
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'roles' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permissions' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
