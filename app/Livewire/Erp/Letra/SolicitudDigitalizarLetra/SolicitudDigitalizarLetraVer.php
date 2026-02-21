<?php

namespace App\Livewire\Erp\Letra\SolicitudDigitalizarLetra;

use App\Models\EnvioCavali;
use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Models\SolicitudDigitalizarLetra;
use App\Services\CavaliService;
use App\Exports\Letra\CavaliExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Ver Solicitud de Letra Digital')]
class SolicitudDigitalizarLetraVer extends Component
{
    public SolicitudDigitalizarLetra $solicitud;

    // Campos editables
    public $estado_solicitud_digitalizar_letra_id;

    protected function rules()
    {
        return [
            'estado_solicitud_digitalizar_letra_id' => 'required|exists:estado_solicitud_digitalizar_letras,id',
        ];
    }

    public function mount($id)
    {
        $this->solicitud = SolicitudDigitalizarLetra::with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado'])->findOrFail($id);
        $this->estado_solicitud_digitalizar_letra_id = $this->solicitud->estado_solicitud_digitalizar_letra_id;
    }

    public function update()
    {
        abort_unless(auth()->user()->can('solicitud-digitalizar-letra.editar'), 403);

        $this->validate();

        try {
            DB::beginTransaction();

            $this->solicitud->update([
                'estado_solicitud_digitalizar_letra_id' => $this->estado_solicitud_digitalizar_letra_id,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Cambios guardados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error SolicitudDigitalizarLetraEditar@update: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudieron guardar los cambios.']);
        }
    }

    public function enviarIndividual()
    {
        $this->authorize('solicitud-digitalizar-letra.ejecutar-cron-letra');

        $idEstadoPendiente = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::PENDIENTE);
        $idEstadoEnviado = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::ENVIADO);

        if ($this->solicitud->estado_solicitud_digitalizar_letra_id !== $idEstadoPendiente) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No disponible',
                'text' => 'La solicitud debe estar en estado PENDIENTE para ser enviada.'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $fecha = now()->toDateString();
            $unidad = $this->solicitud->unidadNegocio;

            // Evita duplicar cortes para el mismo día y unidad
            $envio = EnvioCavali::firstOrCreate(
                [
                    'fecha_corte' => $fecha,
                    'unidad_negocio_id' => $unidad->id,
                ],
                [
                    'estado_solicitud_digitalizar_letra_id' => $idEstadoPendiente,
                ]
            );

            $envio->solicitudes()->syncWithoutDetaching([$this->solicitud->id]);

            // Generar Excel (actualizado con todas las letras del envío actual)
            $razonSocialSanitizada = preg_replace('/[^A-Za-z0-9_\-]/', '_', $unidad->razon_social);
            $fileName = "CAVALI_{$razonSocialSanitizada}_{$fecha}.xlsx";
            $path = "cavali/{$fecha}/{$fileName}";

            Excel::store(new CavaliExport($envio), $path, 'local');

            // Actualizar envío
            $envio->update([
                'estado_solicitud_digitalizar_letra_id' => $idEstadoEnviado,
                'enviado_at' => now(),
                'archivo_zip' => $path,
            ]);

            // Actualizar solicitud
            $this->solicitud->update(['estado_solicitud_digitalizar_letra_id' => $idEstadoEnviado]);
            $this->estado_solicitud_digitalizar_letra_id = $idEstadoEnviado;

            // Enviar correo (simplificado para individual)
            Mail::raw(
                "Estimados, se ha enviado una letra individual a desmaterializar.\n\nEmpresa: {$unidad->razon_social}\nLetra: {$this->solicitud->codigo_venta}-{$this->solicitud->numero_cuota}",
                function ($message) use ($path, $fileName, $razonSocialSanitizada) {
                    $message->to('PROGRAMADOR@aybarsac.com')
                        ->cc(['mersmith14@gmail.com'])
                        ->subject("Letra Individual - {$razonSocialSanitizada}")
                        ->attach(Storage::path($path), [
                            'as' => $fileName,
                        ]);
                }
            );

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Enviado',
                'text' => 'La solicitud ha sido enviada a digitalizar correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error enviarIndividual: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Hubo un problema al procesar el envío: ' . $e->getMessage()
            ]);
        }
    }

    public function validarIndividual(CavaliService $service)
    {
        $this->authorize('solicitud-digitalizar-letra.validar-cron-letra');

        $idEstadoEnviado = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::ENVIADO);
        $idEstadoAprobado = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::APROBADO);

        if ($this->solicitud->estado_solicitud_digitalizar_letra_id !== $idEstadoEnviado) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No disponible',
                'text' => 'La solicitud debe estar en estado ENVIADO para ser validada.'
            ]);
            return;
        }

        try {
            $nroCavali = ($this->solicitud->codigo_cuota ?? '') . ($this->solicitud->numero_cuota ?? '');

            if (empty($nroCavali)) {
                throw new \Exception('No se pudo generar el nroCavali (falta código o número de cuota).');
            }

            $result = $service->obtenerConstanciaCancelacion($nroCavali);

            if (($result['codigo'] ?? '') === '001' && !empty($result['base64'])) {
                $this->solicitud->update([
                    'estado_solicitud_digitalizar_letra_id' => $idEstadoAprobado,
                ]);
                $this->estado_solicitud_digitalizar_letra_id = $idEstadoAprobado;

                $this->dispatch('alertaLivewire', [
                    'type' => 'success',
                    'title' => 'Validado',
                    'text' => 'La solicitud ha sido validada correctamente en Cavali.'
                ]);
            } else {
                $this->dispatch('alertaLivewire', [
                    'type' => 'info',
                    'title' => 'Aún pendiente',
                    'text' => 'Cavali aún no registra la cancelación de esta letra (Código: ' . ($result['codigo'] ?? 'N/A') . ').'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error validarIndividual: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Error al validar con Cavali: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.letra.solicitud-digitalizar-letra.solicitud-digitalizar-letra-ver', [
            'estados' => EstadoSolicitudDigitalizarLetra::where('activo', true)->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
