<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\EntregaFest\PreInvitacionPropietarioMail;
use App\Mail\EntregaFest\PreInvitacionCopropietarioMail;
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
    public $estado_contrato_preeliminar_emitido = '';

    #[Url(keep: true)]
    public $estado_firma_contrato_firmado = '';

    #[Url(keep: true)]
    public $grupo = '';

    #[Url(keep: true)]
    public $perPage = 20;

    // Catálogos
    public $proyectos = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'proyecto_id', 'estado_backoffice', 'estado_contrato_preeliminar_emitido', 'estado_firma_contrato_firmado', 'grupo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id', 'estado_backoffice', 'estado_contrato_preeliminar_emitido', 'estado_firma_contrato_firmado', 'grupo']);
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
                $this->estado_contrato_preeliminar_emitido,
                $this->estado_firma_contrato_firmado,
                $this->grupo,
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
                true
            ),
            'prospectos_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    public function enviarPreInvitacionN8N()
    {
        // 1. Buscamos la plantilla configurada para este evento
        $plantilla = $this->evento->plantillas()->where('tipo', 'pre-invitacion')->first();

        $contactos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->whereNotNull('email')
            ->with(['copropietarios', 'entregaFest'])
            ->get()
            ->map(function ($prospecto) {

                // Preparamos el Mail del PROPIETARIO para obtener su link y HTML
                $mailPropietario = new PreInvitacionPropietarioMail($prospecto);

                return [
                    'id' => $prospecto->id,
                    'email' => $prospecto->email,
                    'nombres' => $prospecto->nombres,
                    'celular' => $prospecto->celular,
                    'dni' => $prospecto->dni,
                    'link' => $mailPropietario->link,
                    'html' => $mailPropietario->render(),

                    'copropietarios' => $prospecto->copropietarios->map(function ($copro) {
                        $mailCopro = new PreInvitacionCopropietarioMail($copro);
                        return [
                            'id' => $copro->id,
                            'nombres' => $copro->nombres,
                            'email' => $copro->email,
                            'celular' => $copro->celular,
                            'dni' => $copro->dni,
                            'link' => $mailCopro->link,
                            'html' => $mailCopro->render(),
                        ];
                    })
                ];
            })
            ->toArray();

        // 2. Enviamos todo el paquete a n8n incluyendo la data de la PLANTILLA
        Http::post(config('services.n8n.webhook_entrega_fest_pre_invitacion'), [
            'contactos' => $contactos,
            'evento' => $this->evento->nombre,
            'plantilla' => [
                'titulo' => $plantilla?->titulo ?? 'Pre-invitación: ' . $this->evento->nombre,
                'subtitulo' => $plantilla?->subtitulo ?? '',
                'descripcion' => $plantilla?->descripcion ?? '',
                'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $this->evento->getFirstMediaUrl('imagen_invitacion'),
                'link_boton' => $plantilla?->link_boton ?? '',
            ]
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Pre-invitaciones enviadas!',
            'text' => 'Se ha enviado la plantilla de "' . ($plantilla?->titulo ?? 'Pre-invitación') . '" a ' . count($contactos) . ' prospectos ✅',
        ]);
    }

    public function render()
    {
        $items = ProspectoEntregaFest::query()
            ->with(['proyecto', 'user', 'invitado'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombres', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                        ->orWhere('email', 'like', '%' . $this->buscar . '%')
                        ->orWhere('celular', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->estado_backoffice, fn($q) => $q->where('estado_backoffice', $this->estado_backoffice))
            ->when($this->estado_contrato_preeliminar_emitido, fn($q) => $q->where('estado_contrato_preeliminar_emitido', $this->estado_contrato_preeliminar_emitido))
            ->when($this->estado_firma_contrato_firmado, fn($q) => $q->where('estado_firma_contrato_firmado', $this->estado_firma_contrato_firmado))
            ->when($this->grupo, fn($q) => $q->where('grupo', $this->grupo))
            ->orderBy('id', 'desc')
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
