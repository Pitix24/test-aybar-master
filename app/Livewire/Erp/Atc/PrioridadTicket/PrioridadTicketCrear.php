<?php

namespace App\Livewire\Erp\Atc\PrioridadTicket;

use App\Models\PrioridadTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Prioridad de Ticket')]
class PrioridadTicketCrear extends Component
{
    public $nombre = '';
    public $tiempo_permitido = '';
    public $color = '#3b82f6';
    public $icono = 'fa-solid fa-flag';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:prioridad_tickets,nombre',
            'tiempo_permitido' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre de la prioridad',
            'tiempo_permitido' => 'tiempo permitido (horas)',
            'color' => 'color informativo',
            'icono' => 'icono representativo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('prioridad-ticket.accion-crear');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            PrioridadTicket::create([
                'nombre' => trim($this->nombre),
                'tiempo_permitido' => $this->tiempo_permitido,
                'color' => $this->color ?? '#3b82f6',
                'icono' => $this->icono ?? 'fa-solid fa-flag',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'La prioridad de ticket se creó correctamente.'
            ]);

            return redirect()->route('erp.prioridad-ticket.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('prioridad_ticket')->error("[PRIORIDAD TICKET] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear la prioridad de ticket.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.prioridad-ticket.prioridad-ticket-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
