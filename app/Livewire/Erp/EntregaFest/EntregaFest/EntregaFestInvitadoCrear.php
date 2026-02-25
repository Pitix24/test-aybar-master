<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Generar Invitado - Entrega Fest')]
class EntregaFestInvitadoCrear extends Component
{
    public EntregaFest $evento;

    public $prospecto_entrega_fest_id = '';
    public $cantidad_acompanantes_permitidos = 0;
    public $confirmado = false;

    public $prospectos = [];

    protected function rules()
    {
        return [
            'prospecto_entrega_fest_id' => 'required|exists:prospecto_entrega_fests,id',
            'cantidad_acompanantes_permitidos' => 'required|integer|min:0',
            'confirmado' => 'boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'prospecto_entrega_fest_id' => 'prospecto',
            'cantidad_acompanantes_permitidos' => 'cantidad de acompañantes',
            'confirmado' => 'confirmación',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->loadProspectos();
    }

    public function loadProspectos()
    {
        // Solo prospectos del evento que NO sean invitados ya
        $this->prospectos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->whereDoesntHave('invitado')
            ->orderBy('nombres')
            ->get();
    }

    public function store()
    {
        $this->authorize('entrega-fest.invitados');

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

            // Generar código único: INV-EVENT-ID-RAND
            $codigo = 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

            InvitadoEntregaFest::create([
                'entrega_fest_id' => $this->evento->id,
                'prospecto_entrega_fest_id' => $this->prospecto_entrega_fest_id,
                'codigo_invitado' => $codigo,
                'cantidad_acompanantes_permitidos' => $this->cantidad_acompanantes_permitidos,
                'confirmado' => $this->confirmado,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Generado!',
                'text' => 'Invitación generada con código: ' . $codigo
            ]);

            return redirect()->route('erp.entrega-fest.vista.invitados', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[INVITADO CREAR] Error al generar invitación: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo generar la invitación.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-invitado-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
