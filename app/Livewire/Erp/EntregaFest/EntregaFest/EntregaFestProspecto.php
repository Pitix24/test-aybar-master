<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\Proyecto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregaFest\EntregaFestProspectoExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\EntregaFest\AsistenciaLinkMail;
use App\Mail\EntregaFest\AsistenciaLinkCopropietarioMail;
use App\Services\WhatsappService;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\Cliente;

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
    public $estado = '';

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
        if (in_array($property, ['buscar', 'proyecto_id', 'estado', 'estado_firma_contrato_firmado', 'grupo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id', 'estado', 'estado_firma_contrato_firmado', 'grupo']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('entrega-fest.prospectos');

        return Excel::download(
            new EntregaFestProspectoExport(
                $this->evento->id,
                $this->buscar,
                $this->proyecto_id,
                $this->estado,
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
        $this->authorize('entrega-fest.prospectos');

        return Excel::download(
            new EntregaFestProspectoExport(
                $this->evento->id,
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

    public function enviarCorreos()
    {
        // 1. Titulares aprobados sin invitación con email
        $prospectos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->where('estado_backoffice', 'aprobado')
            ->whereDoesntHave('invitado')
            ->whereNotNull('email')
            ->get();

        // 2. Copropietarios de prospectos aprobados, sin invitación y con email
        $copropietarios = \App\Models\CopropietarioEntregaFest::whereHas('prospecto', function ($q) {
            $q->where('entrega_fest_id', $this->evento->id)
                ->where('estado_backoffice', 'aprobado');
        })
            ->whereDoesntHave('invitado')
            ->whereNotNull('email')
            ->with('prospecto') // para el link de asistencia
            ->get();

        if ($prospectos->isEmpty() && $copropietarios->isEmpty()) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Información',
                'text' => 'No hay prospectos ni copropietarios aprobados pendientes de invitación con correo registrado.'
            ]);
            return;
        }

        $enviados = 0;

        // Enviar a titulares
        foreach ($prospectos as $prospecto) {
            try {
                Mail::to($prospecto->email)->send(new AsistenciaLinkMail($prospecto));
                $enviados++;
            } catch (\Exception $e) {
                \Log::error("[ENTREGA-FEST] Error correo titular {$prospecto->email}: " . $e->getMessage());
            }
        }

        // Enviar a copropietarios con su propio mail y su propio link
        foreach ($copropietarios as $copropietario) {
            try {
                Mail::to($copropietario->email)->send(new AsistenciaLinkCopropietarioMail($copropietario));
                $enviados++;
            } catch (\Exception $e) {
                \Log::error("[ENTREGA-FEST] Error correo copropietario {$copropietario->email}: " . $e->getMessage());
            }
        }

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Completado!',
            'text' => "Se enviaron $enviados correos (titulares + copropietarios)."
        ]);
    }

    public function enviarWhatsapp(WhatsappService $whatsapp)
    {
        \Log::info('[WSP] ===== MÉTODO INVOCADO =====');

        // 1. Titulares aprobados sin invitación con celular
        $prospectos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->where('estado_backoffice', 'aprobado')
            ->whereDoesntHave('invitado')
            ->whereNotNull('celular')
            ->get();

        // 2. Copropietarios sin invitación con celular
        $copropietarios = \App\Models\CopropietarioEntregaFest::whereHas('prospecto', function ($q) {
            $q->where('entrega_fest_id', $this->evento->id)
                ->where('estado_backoffice', 'aprobado');
        })
            ->whereDoesntHave('invitado')
            ->whereNotNull('celular')
            ->with('prospecto')
            ->get();

        \Log::info('[WSP] Prospectos encontrados: ' . $prospectos->count());
        \Log::info('[WSP] Copropietarios encontrados: ' . $copropietarios->count());

        if ($prospectos->isEmpty() && $copropietarios->isEmpty()) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Información',
                'text' => 'No hay prospectos ni copropietarios aprobados pendientes de invitación con celular registrado.'
            ]);
            return;
        }

        $enviados = 0;

        // ── Helper para formatear celular peruano ─────────────────────
        $formatearCelular = function (string $raw): string {
            $cel = preg_replace('/\D/', '', $raw);
            return strlen($cel) === 9 ? '51' . $cel : $cel;
        };

        // ── Enviar a TITULARES ────────────────────────────────────────
        foreach ($prospectos as $prospecto) {
            $link = route('public.entrega-fest.asistencia', [$this->evento->slug, $prospecto->id]);
            $mensaje = "Hola *{$prospecto->nombres}*, ya tenemos tu evaluación lista para el evento *{$this->evento->nombre}*. Confirma tu asistencia aquí: $link";
            $celular = $formatearCelular($prospecto->celular);

            \Log::info("[WSP] Enviando a: {$prospecto->nombres} | cel: {$celular}");

            $response = $whatsapp->sendText($celular, $mensaje);

            \Log::info('[WSP] Respuesta API titular:', ['ok' => (bool) $response, 'data' => $response]);

            if ($response) {
                $enviados++;
                $cliente = Cliente::where('dni', $prospecto->dni)->first();
                $contacto = WhatsappContacto::updateOrCreate(
                    ['wa_id' => $celular],
                    ['nombre_wa' => $prospecto->nombres, 'numero_celular' => $prospecto->celular, 'cliente_id' => $cliente?->id]
                );
                $conversacion = WhatsappConversacion::firstOrCreate(
                    ['contacto_id' => $contacto->id],
                    ['cliente_id' => $cliente?->id, 'estado' => 'asignado', 'departamento_destino' => 'backoffice', 'agente_id' => auth()->id()]
                );
                $conversacion->update(['last_message_at' => now()]);
                WhatsappMensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'direccion' => 'saliente',
                    'tipo' => 'texto',
                    'contenido' => $mensaje,
                    'wa_message_id' => $response['messages'][0]['id'] ?? 'PROS_' . uniqid(),
                    'estado' => 'enviado',
                ]);
            }
        }

        // ── Enviar a COPROPIETARIOS ───────────────────────────────────
        foreach ($copropietarios as $copropietario) {
            // Link a SU PROPIA página de confirmación
            $link = route('public.entrega-fest.asistencia.copropietario', [$this->evento->slug, $copropietario->id]);
            $mensaje = "Hola *{$copropietario->nombres}*, ya tienes evaluación lista para el evento *{$this->evento->nombre}*. Confirma tu asistencia aquí: $link";
            $celular = $formatearCelular($copropietario->celular);

            $response = $whatsapp->sendText($celular, $mensaje);
            if ($response) {
                $enviados++;
                $cliente = Cliente::where('dni', $copropietario->dni)->first();
                $contacto = WhatsappContacto::updateOrCreate(
                    ['wa_id' => $celular],
                    ['nombre_wa' => $copropietario->nombres, 'numero_celular' => $copropietario->celular, 'cliente_id' => $cliente?->id]
                );
                $conversacion = WhatsappConversacion::firstOrCreate(
                    ['contacto_id' => $contacto->id],
                    ['cliente_id' => $cliente?->id, 'estado' => 'asignado', 'departamento_destino' => 'backoffice', 'agente_id' => auth()->id()]
                );
                $conversacion->update(['last_message_at' => now()]);
                WhatsappMensaje::create([
                    'conversacion_id' => $conversacion->id,
                    'direccion' => 'saliente',
                    'tipo' => 'texto',
                    'contenido' => $mensaje,
                    'wa_message_id' => $response['messages'][0]['id'] ?? 'COPROP_' . uniqid(),
                    'estado' => 'enviado',
                ]);
            }
        }

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Completado!',
            'text' => "Se enviaron $enviados mensajes de WhatsApp (titulares + copropietarios)."
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
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
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->estado_firma_contrato_firmado, fn($q) => $q->where('estado_firma_contrato_firmado', $this->estado_firma_contrato_firmado))
            ->when($this->grupo, fn($q) => $q->where('grupo', $this->grupo))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-prospecto', [
            'items' => $items
        ]);
    }
}
