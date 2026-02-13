<?php

namespace App\Livewire\Cliente\Lote;

use App\Services\AybarSlinService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
class LoteTodo extends Component
{
    public $cliente_encontrado = null;

    public $razones_sociales = [];
    public $razon_social_id = "";
    public $razon_social_select;

    public $lotes = null;
    public $lote_select = null;
    public $cronograma_estado_cuenta = [];

    public function mount(AybarSlinService $slinService, $clienteEncontradoCrear = null, $razonesSocialesCrear = null)
    {
        try {
            if ($clienteEncontradoCrear && $razonesSocialesCrear) {
                $this->cliente_encontrado = $clienteEncontradoCrear;
                $this->razones_sociales = $razonesSocialesCrear;
                return;
            }

            if (auth()->user()->necesitaActualizarDatosPersonales() || auth()->user()->necesitaActualizarDireccion()) {
                session()->flash('info', 'Para poder acceder a tus proyectos, es obligatorio que actualices tus datos.');
                return;
            }

            $perfil = Auth::user()->perfilCliente;
            if (!$perfil || !$perfil->dni) {
                session()->flash('error', 'No se encontró información de perfil para el usuario actual.');
                return;
            }

            $cliente = $slinService->getCliente($perfil->dni);

            if (empty($cliente)) {
                session()->flash('error', 'No se pudo obtener el perfil de sus lotes. Por favor, inténtelo más tarde.');
                return;
            }

            $this->cliente_encontrado = $cliente;
            $this->razones_sociales = $cliente['empresas'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error en mount LoteTodo: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al cargar la información. Intente nuevamente.');
        }
    }

    public function updatedRazonSocialId($value, AybarSlinService $slinService)
    {
        $this->reset(['lotes', 'lote_select', 'razon_social_select']);

        if (empty($value)) {
            return;
        }

        try {
            $this->razon_social_select = collect($this->razones_sociales)->firstWhere('id_empresa', $value);

            if (!$this->razon_social_select) {
                return;
            }

            $this->lotes = $slinService->getLotes(
                $this->razon_social_select['codigo'],
                $this->razon_social_select['id_empresa']
            );

            if (empty($this->lotes)) {
                session()->flash('info', 'No se encontraron lotes asociados a esta razón social.');
            }
        } catch (\Exception $e) {
            Log::error('Error en updatedRazonSocialId: ' . $e->getMessage());
            session()->flash('error', 'No se pudieron cargar los lotes. Intente nuevamente.');
        }
    }

    public function verCronogramaEstadoCuenta(array $lote, AybarSlinService $slinService)
    {
        try {
            $this->lote_select = $lote;

            $params = [
                'id_empresa' => $this->lote_select['id_empresa'],
                'lote' => $this->lote_select['id_proyecto'] . $this->lote_select['id_etapa'] . '-' . $this->lote_select['id_manzana'] . '-' . $this->lote_select['id_lote'],
                'id_cliente' => $this->lote_select['id_cliente'],
                'contrato' => $this->lote_select['contrato'] ?? '',
                'servicio' => $this->lote_select['servicio'] ?? '02',
            ];

            $data = $slinService->getCronogramaEstadoCuenta($params);

            if (empty($data)) {
                $this->cronograma_estado_cuenta = [];
                session()->flash('error', 'No se pudo obtener el cronograma. Por favor, inténtelo más tarde.');
                return;
            }

            $this->cronograma_estado_cuenta = $data;
        } catch (\Exception $e) {
            Log::error('Error en verCronogramaEstadoCuenta: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un problema al obtener el cronograma.');
            $this->cerrarVista();
        }
    }

    public function cerrarVista()
    {
        $this->lote_select = null;
        $this->cronograma_estado_cuenta = [];
    }

    public function descargarPDFcronograma()
    {
        if (!$this->lote_select || empty($this->cronograma_estado_cuenta)) {
            session()->flash('error', 'Debe seleccionar un lote antes de descargar.');
            return;
        }

        try {
            $pdf = Pdf::loadView('pdf.cronograma', [
                'estado_cuenta' => $this->cronograma_estado_cuenta,
            ]);

            return response()->streamDownload(
                fn() => print ($pdf->output()),
                'cronograma-' . ($this->lote_select['id_recaudo'] ?? 'descarga') . '.pdf'
            );
        } catch (\Exception $e) {
            Log::error('Error en descargarPDFcronograma: ' . $e->getMessage());
            session()->flash('error', 'Error al generar el PDF del cronograma.');
        }
    }

    public function descargarPDFestadoCuenta()
    {
        if (!$this->lote_select || empty($this->cronograma_estado_cuenta)) {
            session()->flash('error', 'Debe seleccionar un lote antes de descargar.');
            return;
        }

        try {
            $pdf = Pdf::loadView('pdf.estado-cuenta', [
                'estado_cuenta' => $this->cronograma_estado_cuenta,
            ])->setPaper('a4', 'landscape');

            return response()->streamDownload(
                fn() => print ($pdf->output()),
                'estado-cuenta-' . ($this->lote_select['id_recaudo'] ?? 'descarga') . '.pdf'
            );
        } catch (\Exception $e) {
            Log::error('Error en descargarPDFestadoCuenta: ' . $e->getMessage());
            session()->flash('error', 'Error al generar el PDF del estado de cuenta.');
        }
    }

    public function render()
    {
        return view('livewire.cliente.lote.lote-todo');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
