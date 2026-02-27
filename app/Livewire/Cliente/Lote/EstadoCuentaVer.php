<?php

namespace App\Livewire\Cliente\Lote;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaPago;
use App\Models\SolicitudDigitalizarLetra;
use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Services\CavaliService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class EstadoCuentaVer extends Component
{
    public $lote;
    public $estado_cuenta = [];

    public $detalle = [];
    public $cuota = null;
    public $cuotaCavali = null;

    public function mount($lote, $estado_cuenta)
    {
        $this->lote = $lote;
        $this->estado_cuenta = $estado_cuenta;
        $this->detalle = $estado_cuenta['Cuotas'] ?? [];

        $this->loadComprobantesYActualizarCronograma();
    }

    public function seleccionarCuota($cuota)
    {
        if (auth()->user()->rol !== 'cliente') {
            session()->flash('error', '¡No está autorizado! Solo el cliente puede subir sus evidencias.');
            return;
        }

        $this->cuota = $cuota;
    }

    #[On('actualizarCronograma')]
    public function loadComprobantesYActualizarCronograma()
    {
        try {
            $service = app(CavaliService::class);
            $rechazadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::RECHAZADO);
            $aprobadoLetraId = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::APROBADO);

            $comprobantes = SolicitudEvidenciaPago::query()
                ->whereIn('codigo_cuota', collect($this->detalle)->pluck('idCuota'))
                ->withCount([
                    'evidencias',
                    'evidencias as evidencias_rechazadas_count' => function ($q) use ($rechazadoId) {
                        $q->where('estado_solicitud_evidencia_pago_id', $rechazadoId ?? 0);
                    },
                ])
                ->get()
                ->keyBy('codigo_cuota');

            $letras = SolicitudDigitalizarLetra::query()
                ->whereIn('codigo_cuota', collect($this->detalle)->pluck('idCuota'))
                ->get()
                ->keyBy('codigo_cuota');

            $this->detalle = collect($this->detalle)->map(function ($cuota) use ($comprobantes, $letras, $aprobadoLetraId, $service) {
                $solicitud = $comprobantes->get($cuota['idCuota']);

                $total = $solicitud?->evidencias_count ?? 0;
                $rechazadas = $solicitud?->evidencias_rechazadas_count ?? 0;
                $validas = $total - $rechazadas;

                $cuota['comprobantes_count'] = $total;
                $cuota['comprobantes_rechazados_count'] = $rechazadas;

                $estaAprobada = $solicitud?->esta_aprobada ?? false;

                $cuota['puede_subir'] = !$estaAprobada && ($validas < 2);

                $solicitudDigital = $letras->get($cuota['idCuota']);
                $cuota['tiene_solicitud_digitalizacion'] = (bool) $solicitudDigital;
                $cuota['letra_digitalizada_local'] = ($solicitudDigital?->estado_solicitud_digitalizar_letra_id == $aprobadoLetraId);

                // Validación proactiva Cavali
                $cuota['tiene_constancia_cavali'] = false;
                if (!empty($cuota['NroCavali']) && ($cuota['SaldoPendiente'] ?? 0) == 0) {
                    try {
                        $respuesta = $service->consultar($cuota['NroCavali']);
                        if (!empty($respuesta['base64']) && ($respuesta['codigo'] ?? null) === '001') {
                            $cuota['tiene_constancia_cavali'] = true;
                        }
                    } catch (\Exception $e) {
                        // Ignoramos errores de red individuales para no bloquear la carga
                    }
                }

                return $cuota;
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Error en loadComprobantesYActualizarCronograma: ' . $e->getMessage());
            session()->flash('error', 'Error al sincronizar la información del cronograma.');
        }
    }

    public function verConstanciaCavali($cuota, CavaliService $service)
    {
        if (empty($cuota['NroCavali'])) {
            session()->flash('info', 'Esta cuota no cuenta con un número de Cavali asociado.');
            return;
        }

        try {
            // Si ya validamos en la carga inicial que tiene éxito, procedemos a abrir la URL
            if ($cuota['tiene_constancia_cavali'] ?? false) {
                $url = route('cavali.constancia.ver', $cuota['NroCavali']);
                $this->dispatch('abrirUrlLivewire', $url);
                return;
            }

            // Si por alguna razón no estaba validada (p.ej. error de red previo), lo intentamos una vez más
            $respuesta = $service->consultar($cuota['NroCavali']);

            if (
                empty($respuesta['base64']) ||
                ($respuesta['codigo'] ?? null) !== '001'
            ) {
                $this->cuotaCavali = $cuota;
                return;
            }

            $url = route('cavali.constancia.ver', $cuota['NroCavali']);
            $this->dispatch('abrirUrlLivewire', $url);
        } catch (\Exception $e) {
            Log::error('Error al obtener constancia Cavali: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un problema al conectar con el servicio de Cavali.');
        }
    }

    #[On('cerrarModalEvidenciaPagoOn')]
    public function cerrarModalEvidenciaPago()
    {
        $this->cuota = null;
    }

    #[On('cerrarModalCavaliOn')]
    public function cerrarModalCavali()
    {
        $this->cuotaCavali = null;
    }

    public function render()
    {
        return view('livewire.cliente.lote.estado-cuenta-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
