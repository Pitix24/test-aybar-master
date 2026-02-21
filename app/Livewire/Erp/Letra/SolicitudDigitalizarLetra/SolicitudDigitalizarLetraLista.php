<?php

namespace App\Livewire\Erp\Letra\SolicitudDigitalizarLetra;

use App\Jobs\ValidarEnviosCavaliDiariosJob;
use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Models\Proyecto;
use App\Models\SolicitudDigitalizarLetra;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\Letra\SolicitudDigitalizarLetraExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\GenerarEnviosCavaliDiariosJob;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Solicitudes de Letras Digitales')]
class SolicitudDigitalizarLetraLista extends Component
{
    use WithPagination;

    #[Url(as: 'q', keep: true)]
    public $buscar = '';

    #[Url(keep: true)]
    public $estado_id = '';

    #[Url(keep: true)]
    public $unidad_negocio_id = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $fecha_inicio = '';

    #[Url(keep: true)]
    public $fecha_fin = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public $estados = [];
    public $unidades_negocios = [];
    public $proyectos = [];

    public function mount()
    {
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->format('Y-m-d');

        $this->estados = EstadoSolicitudDigitalizarLetra::all();
        $this->unidades_negocios = UnidadNegocio::all();

        if ($this->unidad_negocio_id) {
            $this->loadProyectos();
        }
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        $this->proyectos = [];

        if ($value) {
            $this->loadProyectos();
        }
        $this->resetPage();
    }

    public function loadProyectos()
    {
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->get();
        }
    }

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'estado_id',
                'unidad_negocio_id',
                'proyecto_id',
                'fecha_inicio',
                'fecha_fin',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset([
            'buscar',
            'estado_id',
            'unidad_negocio_id',
            'proyecto_id',
        ]);
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->format('Y-m-d');
        $this->perPage = 20;
        $this->resetPage();
    }

    public function ejecutarCronLetra()
    {
        $this->authorize('solicitud-digitalizar-letra.ejecutar-cron-letra');

        try {
            GenerarEnviosCavaliDiariosJob::dispatchSync();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Proceso Completado',
                'text' => 'El cron de letras se ha ejecutado exitosamente.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo iniciar el proceso: ' . $e->getMessage()
            ]);
        }
    }

    public function validarCronLetra()
    {
        $this->authorize('solicitud-digitalizar-letra.validar-cron-letra');

        try {
            ValidarEnviosCavaliDiariosJob::dispatchSync();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Proceso Completado',
                'text' => 'El cron de letras se ha ejecutado exitosamente.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo iniciar el proceso: ' . $e->getMessage()
            ]);
        }
    }

    public function exportExcelFiltro()
    {
        $this->authorize('solicitud-digitalizar-letra.exportar-filtro');

        $nombreArchivo = 'letras_filtro_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SolicitudDigitalizarLetraExport(
            $this->buscar,
            $this->estado_id,
            $this->unidad_negocio_id,
            $this->proyecto_id,
            $this->fecha_inicio,
            $this->fecha_fin,
            $this->perPage,
            $this->getPage(),
            false
        ), $nombreArchivo);
    }

    public function exportExcelTodo()
    {
        $this->authorize('solicitud-digitalizar-letra.exportar-todo');

        $nombreArchivo = 'letras_todo_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SolicitudDigitalizarLetraExport(
            '',
            '',
            '',
            '',
            $this->fecha_inicio,
            $this->fecha_fin,
            null,
            null,
            true
        ), $nombreArchivo);
    }

    public function render()
    {
        $items = SolicitudDigitalizarLetra::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado'])
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('id', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cliente', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cuota', 'like', "%{$buscar}%")
                        ->orWhereHas('userCliente', function ($qUser) use ($buscar) {
                            $qUser->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('userCliente.perfilCliente', function ($qCliente) use ($buscar) {
                            $qCliente->where('dni', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado_id, fn($q) => $q->where('estado_solicitud_digitalizar_letra_id', $this->estado_id))
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->fecha_inicio, fn($q) => $q->whereDate('created_at', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('created_at', '<=', $this->fecha_fin))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.letra.solicitud-digitalizar-letra.solicitud-digitalizar-letra-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
