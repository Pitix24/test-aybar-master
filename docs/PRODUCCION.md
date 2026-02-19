***********ESTO PONERLO EN LA RAIZ DEL PROYECTO***********
/public_html/.htaccess

<IfModule mime_module>
  AddHandler application/x-httpd-ea-php81 .php .php8 .phtml
</IfModule>

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

////ESTO TAMBIEN PODRIA SER EN CASO NO FUCIONA EL OTRO//////

RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
**********************EN ROUTES WEB**********************
/home/martinca/public_html/routes/web.php

use Illuminate\Support\Facades\Artisan;
Route::get('/symlink', function () {
    Artisan::call('storage:link');
});

////MEDIANTE SSH//////
public_html/
cd public
[u248517392@fr-int-web1790 public]$ ln -s ../storage/app/public storage

y PERMISOS A STORAGE: 755
**********************EXTENSION PHP**********************
fileinfo: HABILITAR
***************EN PRODUCCION ELIMINAR**********************
public\hot

**************DESACTIVAR EL CDN Y BORRAR CACHE***********************
Desactivar/VACIAR CACHE
*************************************

Route::get('/__debug-scheme', function () {
    dd(
        request()->getScheme(),
        request()->secure(),
        request()->headers->all()
    );
});

*************************************
app.php
use Illuminate\Http\Request;

$middleware->trustProxies(
        '*',
        Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
    );
*************************************
AppServiceProvider.php
use Illuminate\Support\Facades\URL;
if (app()->environment('production')) {
        URL::forceScheme('https');
        URL::forceRootUrl('https://plataforma-digital.aybarcorp.com');
    }
*************************************
.env

SESSION_DOMAIN=.aybarcorp.com
SESSION_COOKIE=aybar_plataforma_session
SESSION_SECURE_COOKIE=true
*************************************
{{ request()->cookies->has('laravel_session') ? 'OK' : 'NO COOKIE' }}
*************************************
composer dump-autoload
php artisan optimize:clear
php artisan route:list | grep livewire
php artisan route:list | grep preview
*************************************
app/Providers/AppServiceProvider.php
public function boot(): void
{
    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle)
            ->middleware('web');
    });

    Livewire::setScriptRoute(function ($handle) {
        return Route::get('/livewire/livewire.min.js', $handle)
            ->middleware('web');
    });

    Livewire::setPreviewRoute(function ($handle) {
        return Route::get('/livewire/preview-file/{filename}', $handle)
            ->middleware('web');
    });
}
*************************************
VerifyCsrfToken.php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'livewire/preview-file/*',
    ];
}
*************************************
app.php
->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
        \App\Http\Middleware\VerifyCsrfToken::class,
    ]);
*************************************
Tareas programadas (Cron)

* * * * *	/usr/bin/php /home/u248517392/domains/aybarcorp.com/public_html/plataforma-digital/artisan schedule:run

QUEUE_CONNECTION=sync
*************************************
