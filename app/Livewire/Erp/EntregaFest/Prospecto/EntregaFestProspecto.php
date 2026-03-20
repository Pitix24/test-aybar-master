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

    public function enviarPreInvitacionLaravel()
    {
        $contactos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->whereNotNull('email')
            ->with(['proyecto', 'copropietarios'])
            ->get();

        $contadorEmails = 0;

        /** @var \App\Models\ProspectoEntregaFest $prospecto */
        foreach ($contactos as $prospecto) {
            // Enviar al Propietario
            Mail::to($prospecto->email)->queue(new PreInvitacionPropietarioMail($prospecto));
            $prospecto->update(['enviado_preinvitacion' => true]);
            $contadorEmails++;

            // Enviar a los Copropietarios
            foreach ($prospecto->copropietarios as $copropietario) {
                if ($copropietario->email) {
                    Mail::to($copropietario->email)->queue(new PreInvitacionCopropietarioMail($copropietario));
                    $copropietario->update(['enviado_preinvitacion' => true]);
                    $contadorEmails++;
                }
            }
        }

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Pre-invitaciones enviadas!',
            'text' => 'Se han enviado ' . $contadorEmails . ' correos a propietarios y copropietarios ✅',
        ]);
    }

    public function enviarPreInvitacion()
    {
        $contactos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->whereNotNull('email')
            ->select('email', 'nombres', 'celular', 'dni')
            ->get()
            ->toArray();

        Http::post(config('services.n8n.webhook_email_invitaciones'), [
            'contactos' => $contactos,
            'asunto' => 'Pre-invitación: ' . $this->evento->nombre,
            'evento' => $this->evento->nombre,
            'fecha' => $this->evento->fecha_entrega ?? '',
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Pre-invitaciones enviadas!',
            'text' => 'Pre-invitaciones enviadas a ' . count($contactos) . ' prospectos ✅',
        ]);
    }

    public function enviarPreInvitacionWhatsapp()
    {
        $contactos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->whereNotNull('celular')
            ->select('celular', 'nombres', 'email', 'dni')
            ->get()
            ->toArray();

        Http::post(config('services.n8n.webhook_whatsapp_invitaciones'), [
            'contactos' => $contactos,
            'evento' => $this->evento->nombre,
            'fecha' => $this->evento->fecha ?? '',
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡WhatsApp enviados!',
            'text' => 'Pre-invitaciones WhatsApp enviadas a ' . count($contactos) . ' prospectos ✅',
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
