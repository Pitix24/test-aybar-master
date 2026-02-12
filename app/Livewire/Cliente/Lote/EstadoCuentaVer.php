<?php

namespace App\Livewire\Cliente\Lote;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaPago;
use App\Models\SolicitudDigitalizarLetra;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Services\CavaliService;

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
        $this->detalle = $estado_cuenta['Cuotas'];

        $this->loadComprobantesYActualizarCronograma();
    }

    public function seleccionarCuota($cuota)
    {
        if (auth()->user()->rol !== 'cliente') {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => '¡No esta autorizado! Solo el cliente sube sus evidencias.']);
            return;
        }

        $this->cuota = $cuota;
    }

    #[On('actualizarCronograma')]
    public function loadComprobantesYActualizarCronograma()
    {
        $rechazadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::RECHAZADO);

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

        $this->detalle = collect($this->detalle)->map(function ($cuota) use ($comprobantes, $letras) {
            $solicitud = $comprobantes->get($cuota['idCuota']);

            $total = $solicitud?->evidencias_count ?? 0;
            $rechazadas = $solicitud?->evidencias_rechazadas_count ?? 0;
            $validas = $total - $rechazadas;

            $cuota['comprobantes_count'] = $total;
            $cuota['comprobantes_rechazados_count'] = $rechazadas;

            $estaAprobada = $solicitud?->esta_aprobada ?? false;

            $cuota['puede_subir'] =
                !$estaAprobada
                && ($validas < 2);

            $solicitudDigital = $letras->get($cuota['idCuota']);
            $cuota['tiene_solicitud_digitalizacion'] = (bool) $solicitudDigital;

            return $cuota;
        });
    }

    #[On('cerrarModalEvidenciaPagoOn')]
    public function cerrarModalEvidenciaPago()
    {
        $this->cuota = null;
    }

    public function verConstanciaCavali($cuota, CavaliService $service)
    {
        $nroCavali = $cuota['NroCavali'];

        try {
            $respuesta = $service->obtenerConstanciaCancelacion($nroCavali);
            if (
                empty($respuesta['base64']) ||
                ($respuesta['codigo'] ?? null) !== '001'
            ) {
                $this->cuotaCavali = $cuota;
                return;
            }

            $url = route('cavali.constancia.ver', $nroCavali);
            $this->dispatch('abrirUrlLivewire', $url);
        } catch (\Throwable $e) {
            report($e);
        }
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
}
