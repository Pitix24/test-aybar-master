<?php

namespace App\Livewire\Erp\EntregaFest\Invitado;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Invitado - Entrega Fest')]
class EntregaFestInvitadoEditar extends Component
{
    public EntregaFest $evento;
    public InvitadoEntregaFest $invitado;

    public $cantidad_acompanantes_permitidos = 0;
    public $confirmado = false;
    public $estado_confirmacion;
    public $transporte;
    public $observaciones_asistencia;
    public $codigo_invitado;

    // ── Acompañantes ────────────────────────────────────────────────────
    public $acompanantes = [];

    // Modo: null = lista, 'crear' = formulario nuevo, 'editar' = editando fila
    public $acomp_modo = null;
    public $acomp_editando_id = null;

    // Campos del formulario acompañante
    public $acomp_dni = '';
    public $acomp_nombres = '';
    public $acomp_email = '';
    public $acomp_celular = '';
    // ────────────────────────────────────────────────────────────────────

    public function mount($id, $invitadoId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->invitado = InvitadoEntregaFest::with(['prospecto.proyecto', 'prospecto.user'])
            ->where('entrega_fest_id', $this->evento->id)
            ->findOrFail($invitadoId);

        $this->cantidad_acompanantes_permitidos = $this->invitado->cantidad_acompanantes_permitidos;
        $this->confirmado = $this->invitado->confirmado;
        $this->estado_confirmacion = $this->invitado->estado_confirmacion;
        $this->transporte = $this->invitado->transporte;
        $this->observaciones_asistencia = $this->invitado->observaciones_asistencia;
        $this->codigo_invitado = $this->invitado->codigo_invitado;

        $this->cargarAcompanantes();
    }

    // ════════════════════════════════════════════════════════════════════
    // ACOMPAÑANTES
    // ════════════════════════════════════════════════════════════════════

    public function cargarAcompanantes(): void
    {
        $this->acompanantes = \App\Models\AcompananteEntregaFest::where('invitado_entrega_fest_id', $this->invitado->id)
            ->orderBy('nombres')
            ->get()
            ->toArray();
    }

    public function abrirFormCrear(): void
    {
        /* // Comentado para permitir superar el límite
        if (count($this->acompanantes) >= $this->invitado->cantidad_acompanantes_permitidos) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Límite alcanzado',
                'text' => 'Ya has alcanzado el límite de acompañantes permitidos.',
            ]);
            return;
        }
        */

        $this->resetAcompananteForm();
        $this->acomp_modo = 'crear';
    }

    public function cancelarAcompanante(): void
    {
        $this->resetAcompananteForm();
        $this->acomp_modo = null;
        $this->acomp_editando_id = null;
    }

    private function resetAcompananteForm(): void
    {
        $this->acomp_dni = '';
        $this->acomp_nombres = '';
        $this->acomp_email = '';
        $this->acomp_celular = '';
        $this->resetErrorBag(['acomp_dni', 'acomp_nombres', 'acomp_email', 'acomp_celular']);
    }

    private function reglasAcompanante(bool $esEdicion = false): array
    {
        $dniUnique = 'unique:acompanante_entrega_fests,dni,NULL,id,invitado_entrega_fest_id,' . $this->invitado->id;

        if ($esEdicion && $this->acomp_editando_id) {
            $dniUnique = 'unique:acompanante_entrega_fests,dni,' . $this->acomp_editando_id . ',id,invitado_entrega_fest_id,' . $this->invitado->id;
        }

        return [
            'acomp_dni' => ['required', 'string', 'max:15', $dniUnique],
            'acomp_nombres' => 'required|string|max:255',
            'acomp_email' => 'nullable|email|max:255',
            'acomp_celular' => 'nullable|string|max:20',
        ];
    }

    private function atributosAcompanante(): array
    {
        return [
            'acomp_dni' => 'DNI del acompañante',
            'acomp_nombres' => 'nombres del acompañante',
            'acomp_email' => 'correo del acompañante',
            'acomp_celular' => 'celular del acompañante',
        ];
    }

    public function storeAcompanante(): void
    {
        $this->authorize('invitado.editar');

        /* // Comentado para permitir superar el límite
        if (count($this->acompanantes) >= $this->invitado->cantidad_acompanantes_permitidos) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Límite alcanzado',
                'text' => 'Ya has alcanzado el límite de acompañantes permitidos.',
            ]);
            return;
        }
        */

        $this->validate($this->reglasAcompanante(), [], $this->atributosAcompanante());

        try {
            DB::beginTransaction();

            $prospecto_id = $this->invitado->prospecto_entrega_fest_id ?? $this->invitado->copropietario->prospecto_entrega_fest_id;

            \App\Models\AcompananteEntregaFest::create([
                'prospecto_entrega_fest_id' => $prospecto_id,
                'invitado_entrega_fest_id' => $this->invitado->id,
                'dni' => trim($this->acomp_dni),
                'nombres' => trim($this->acomp_nombres),
                'email' => trim($this->acomp_email) ?: null,
                'celular' => trim($this->acomp_celular) ?: null,
            ]);

            DB::commit();

            $this->cancelarAcompanante();
            $this->cargarAcompanantes();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Agregado!',
                'text' => 'Acompañante registrado correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error('[ACOMPANANTE CREAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo guardar el acompañante.']);
        }
    }

    public function editarAcompanante(int $id): void
    {
        $acomp = \App\Models\AcompananteEntregaFest::where('invitado_entrega_fest_id', $this->invitado->id)->findOrFail($id);

        $this->acomp_editando_id = $acomp->id;
        $this->acomp_dni = $acomp->dni;
        $this->acomp_nombres = $acomp->nombres;
        $this->acomp_email = $acomp->email ?? '';
        $this->acomp_celular = $acomp->celular ?? '';
        $this->acomp_modo = 'editar';
    }

    public function updateAcompanante(): void
    {
        $this->authorize('invitado.editar');

        $this->validate($this->reglasAcompanante(true), [], $this->atributosAcompanante());

        $acomp = \App\Models\AcompananteEntregaFest::where('invitado_entrega_fest_id', $this->invitado->id)
            ->findOrFail($this->acomp_editando_id);

        try {
            DB::beginTransaction();
            $acomp->update([
                'dni' => trim($this->acomp_dni),
                'nombres' => trim($this->acomp_nombres),
                'email' => trim($this->acomp_email) ?: null,
                'celular' => trim($this->acomp_celular) ?: null,
            ]);
            DB::commit();

            $this->cancelarAcompanante();
            $this->cargarAcompanantes();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Acompañante actualizado correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error('[ACOMPANANTE EDITAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo actualizar.']);
        }
    }

    public function eliminarAcompanante(int $id): void
    {
        $this->authorize('invitado.editar');

        $acomp = \App\Models\AcompananteEntregaFest::where('invitado_entrega_fest_id', $this->invitado->id)->findOrFail($id);

        try {
            DB::beginTransaction();
            $acomp->delete();
            DB::commit();
            $this->cargarAcompanantes();
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'Acompañante eliminado.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error('[ACOMPANANTE ELIMINAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo eliminar.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.invitado.entrega-fest-invitado-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
