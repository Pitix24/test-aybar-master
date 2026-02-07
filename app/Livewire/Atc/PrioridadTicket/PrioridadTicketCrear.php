<?php

namespace App\Livewire\Atc\PrioridadTicket;

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
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            PrioridadTicket::create([
                'nombre' => $this->nombre,
                'tiempo_permitido' => $this->tiempo_permitido,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.prioridad-ticket.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear prioridad de ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.atc.prioridad-ticket.prioridad-ticket-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
