<?php

use App\Http\Controllers\CavaliController;
use App\Http\Controllers\SlinController;
use App\Http\Controllers\Web\VerificationController;
use App\Http\Controllers\Web\ConsultaCodigoClienteController;
use App\Livewire\Web\Sesion\ClienteRegistrarLivewire;
use App\Livewire\Web\LibroReclamacion\LibroReclamacionLivewire;
use App\Services\Legal\GmailIndecopiService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->middleware(['web', 'redirect.by.role'])->name('home');

Route::get('/registrar', ClienteRegistrarLivewire::class)->name('registrar.cliente');

Route::post('/email/verification-notification', [VerificationController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/slin/comprobante/ver', [SlinController::class, 'verComprobante'])->name('slin.comprobante.ver');
Route::get('/cavali/constancia/ver/{numeroLetra}', [CavaliController::class, 'verLetra'])->name('cavali.constancia.ver');
Route::get('/cavali/constancia/validar/{numeroLetra}', [CavaliController::class, 'validarLetra'])->name('cavali.constancia.validar');
Route::post('/consulta-codigo-cliente', [ConsultaCodigoClienteController::class, 'consultarClienteDbApi'])->name('consulta-codigo-cliente');

Route::get('/impersonate/leave', function () {
    if (!session()->has('impersonator_id')) {
        return redirect()->route('home');
    }

    $admin = \App\Models\User::find(session('impersonator_id'));
    session()->forget('impersonator_id');
    auth()->login($admin);

    return redirect()->route('erp.cliente.vista.todo');
})->name('impersonate.leave')->middleware('auth');

Route::get('/test-gmail-lectura', function () {
    $gmailService = new \App\Services\Legal\GmailIndecopiService();
    
    $messageId = $gmailService->obtenerUltimoCorreoId();
    if (!$messageId) return "No hay correos.";

    $correo = $gmailService->descargarCorreo($messageId);
    if (!$correo) return "Falló la descarga.";

    $headers = $correo->getPayload()->getHeaders();
    $headerTo = $gmailService->obtenerCabecera($headers, 'To');
    
    // 1. Identificamos la empresa (Fase 4 completada)
    $empresa = $gmailService->identificarUnidadNegocio($headerTo);
    
    // 2. Obtenemos el cuerpo en texto limpio
    $cuerpoNormal = $gmailService->decodificarCuerpo($correo->getPayload());

    // 3. ¡NUEVO! Extraemos los datos exactos (Fase 5 en acción)
    $datosExtraidos = $gmailService->extraerDatosDelCuerpo($cuerpoNormal);

    return response()->json([
        'Estado' => '¡Lectura y Extracción Exitosa!',
        'ID_Mensaje_Gmail' => $messageId,
        'Empresa_Afectada' => $empresa ? $empresa->razon_social : 'NINGUNA',
        'Datos_Parseados' => $datosExtraidos
    ]);
});