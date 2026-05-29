<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Events\EntregaFest\EntregaFestPreInvitacion;
use App\Events\EntregaFest\EntregaFestAsistenciaInvitacionMasivo;
use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregaFest\EntregaFestProspectoExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Prospectos del Evento')]
class EntregaFestProspecto extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $estado_backoffice = '';

    #[Url(keep: true)]
    public $estado_gestor_backoffice = '';

    #[Url(keep: true)]
    public $estado_contrato_preeliminar_emitido = '';

    #[Url(keep: true)]
    public $estado_firma_contrato_firmado = '';

    #[Url(keep: true)]
    public $grupo = '';

    #[Url(keep: true)]
    public $filtro_confirmacion = '';

    #[Url(keep: true)]
    public $filtro_invitacion = '';

    #[Url(keep: true)]
    public $perPage = 20;

    #[Url(keep: true)]
    public $gestor_id = '';

    #[Url(keep: true)]
    public $estado_cliente_id = '';

    public $stats = [];

    // Catálogos
    public $proyectos = [];
    public $usuarios = [];
    public $estados_cliente = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
        $this->usuarios = \App\Models\User::role(['asesor-backoffice', 'supervisor-backoffice'])->get();
        $this->estados_cliente = \App\Models\EntregaFestEstadoCliente::where('activo', true)->orderBy('nombre')->get();

        $this->cargarStats();
    }

    public function cargarStats()
    {
        $baseQuery = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->when($this->proyecto_id, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('proyecto_id', $this->proyecto_id)
                        ->orWhere('reubicado_proyecto_id', $this->proyecto_id);
                });
            })
            ->when($this->estado_cliente_id, fn($q) => $q->where('estado_cliente_id', $this->estado_cliente_id));

        $this->stats = [
            'total' => (clone $baseQuery)->count(),
            'preinvitacion' => (clone $baseQuery)->where('preinvitacion_confirmada', 1)->count(),
            'backoffice' => (clone $baseQuery)->where('estado_backoffice', 'CONFORME')->count(),
            'contrato' => (clone $baseQuery)->where('estado_contrato_preeliminar_emitido', 'CONFORME')->count(),
            'firmados' => (clone $baseQuery)->whereNotNull('fecha_firma')->count(),
        ];
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'proyecto_id', 'estado_backoffice', 'estado_gestor_backoffice', 'estado_contrato_preeliminar_emitido', 'estado_firma_contrato_firmado', 'grupo', 'perPage', 'filtro_confirmacion', 'filtro_invitacion', 'gestor_id', 'estado_cliente_id'])) {
            $this->resetPage();
            $this->cargarStats();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id', 'estado_backoffice', 'estado_gestor_backoffice', 'estado_contrato_preeliminar_emitido', 'estado_firma_contrato_firmado', 'grupo', 'filtro_confirmacion', 'filtro_invitacion', 'gestor_id', 'estado_cliente_id']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('prospecto.exportar-filtro');

        return Excel::download(
            new EntregaFestProspectoExport(
                $this->evento->id,
                $this->buscar,
                $this->proyecto_id,
                $this->estado_backoffice,
                $this->estado_gestor_backoffice,
                $this->estado_contrato_preeliminar_emitido,
                $this->estado_firma_contrato_firmado,
                $this->grupo,
                $this->filtro_confirmacion,
                $this->filtro_invitacion,
                $this->gestor_id,
                $this->estado_cliente_id,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'prospectos_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('prospecto.exportar-todo');

        return Excel::download(
            new EntregaFestProspectoExport(
                $this->evento->id,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                true
            ),
            'prospectos_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    public function enviarPreInvitacion()
    {
        EntregaFestPreInvitacion::dispatch($this->evento);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Solicitud de envío procesada!',
            'text' => 'Se ha enviado la orden de envío masivo de "Pre-invitación" a n8n',
        ]);
    }

    public function enviarInvitacion()
    {
        EntregaFestAsistenciaInvitacionMasivo::dispatch($this->evento);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Solicitud de envío procesada!',
            'text' => 'Se ha enviado la orden de envío masivo de "Invitación" a n8n',
        ]);
    }

    public function render()
    {
        $items = ProspectoEntregaFest::query()
            ->with(['proyecto', 'reubicadoProyecto', 'user', 'invitado', 'gestor', 'copropietarios', 'historialComunicaciones', 'copropietarios.historialComunicaciones', 'estadoCliente'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    // Buscar en titular
                    $q->where('nombres', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                        ->orWhere('email', 'like', '%' . $this->buscar . '%')
                        ->orWhere('celular', 'like', '%' . $this->buscar . '%')
                        // Buscar en copropietarios
                        ->orWhereHas('copropietarios', function ($sub) {
                            $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                                ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                                ->orWhere('email', 'like', '%' . $this->buscar . '%')
                                ->orWhere('celular', 'like', '%' . $this->buscar . '%');
                        });
                });
            })
            ->when($this->proyecto_id, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('proyecto_id', $this->proyecto_id)
                        ->orWhere('reubicado_proyecto_id', $this->proyecto_id);
                });
            })
            ->when($this->estado_backoffice, fn($q) => $q->where('estado_backoffice', $this->estado_backoffice))
            ->when($this->estado_gestor_backoffice, fn($q) => $q->where('estado_gestor_backoffice', $this->estado_gestor_backoffice))
            ->when($this->estado_contrato_preeliminar_emitido, fn($q) => $q->where('estado_contrato_preeliminar_emitido', $this->estado_contrato_preeliminar_emitido))
            ->when($this->estado_firma_contrato_firmado, fn($q) => $q->where('estado_firma_contrato_firmado', $this->estado_firma_contrato_firmado))
            ->when($this->grupo, fn($q) => $q->where('grupo', $this->grupo))
            ->when($this->filtro_confirmacion !== '', function ($query) {
                if ($this->filtro_confirmacion === 'pendiente') {
                    $query->whereNull('preinvitacion_confirmada');
                } else {
                    $query->where('preinvitacion_confirmada', $this->filtro_confirmacion);
                }
            })
            ->when($this->filtro_invitacion !== '', function ($query) {
                if ($this->filtro_invitacion === 'pendiente') {
                    $query->whereNull('invitacion_confirmada');
                } else {
                    $query->where('invitacion_confirmada', $this->filtro_invitacion);
                }
            })
            ->when($this->gestor_id, fn($q) => $q->where('gestor_backoffice_id', $this->gestor_id))
            ->when($this->estado_cliente_id, fn($q) => $q->where('estado_cliente_id', $this->estado_cliente_id))
            ->orderBy('nombres', 'asc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto', [
            'items' => $items
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
