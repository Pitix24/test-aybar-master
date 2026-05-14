<?php

namespace App\Providers;

use App\Models\Erp\Soporte\Soporte;
use App\Policies\SoportePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Soporte::class => SoportePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Registrar las policies automáticamente basado en el mapping arriba
    }
}
