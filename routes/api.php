<?php

use App\Http\Controllers\SlinController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsappController;

Route::middleware('api')->group(function () {

    Route::get('/test-mail', function () {
        Mail::raw('Correo de prueba SMTP', function ($message) {
            $message->to('mersmith14@gmail.com')
                ->subject('Prueba SMTP Laravel');
        });

        return 'Correo enviado';
    });

    Route::get('/symlink', function () {
        Artisan::call('storage:link');
    });

    Route::get('/ping', function () {
        return response()->json(['message' => 'API funcionando correctamente']);
    });

    Route::get('/test-slin/cliente/{dni}', [SlinController::class, 'probarCliente']);
    Route::get('/test-slin/lotes/{id_cliente}/{id_empresa}', [SlinController::class, 'probarLotes']);
    Route::get('/test-slin/cuotas', [SlinController::class, 'probarCuotas']);
    Route::get('/test-slin/estado-cuenta', [SlinController::class, 'probarEstadoCuenta']);
    Route::get('/test-slin/cuota-estado-cuenta', [SlinController::class, 'probarCuotaEstadoCuenta']);
    Route::get('/test-slin/comprobante', [SlinController::class, 'probarComprobante']);
    Route::get('/test-slin/evidencia', [SlinController::class, 'probarEvidencia']);

    Route::get('/slin/cliente/{dni}', [SlinController::class, 'getCliente'])->name('slin.cliente');
    Route::get('/slin/lotes', [SlinController::class, 'getLotes'])->name('slin.lotes');
    Route::get('/slin/cuotas', [SlinController::class, 'getCuotas'])->name('slin.cuotas');
    Route::get('/slin/estado-cuenta', [SlinController::class, 'getEstadoCuenta'])->name('slin.estado-cuenta');
    Route::get('/slin/cuota-estado-cuenta', [SlinController::class, 'getCuotaEstadoCuenta'])->name('slin.cuota-estado-cuenta');
    Route::get('/slin/comprobante', [SlinController::class, 'getComprobante'])->name('slin.comprobante');
    Route::post('/slin/guardar-evidencia', [SlinController::class, 'postGuardarEvidencia'])->name('slin.guardar-evidencia');

    // WhatsApp CRM Webhook
    Route::get('/whatsapp/webhook', [WhatsappController::class, 'verifyWebhook']);
    Route::post('/whatsapp/webhook', [WhatsappController::class, 'handleWebhook']);
});
