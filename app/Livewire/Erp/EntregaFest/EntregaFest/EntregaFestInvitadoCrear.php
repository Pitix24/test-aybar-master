<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\CopropietarioEntregaFest;
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

    // 'titular' o 'copropietario'
    public $tipo_invitado = 'titular';

    public $prospecto_entrega_fest_id = '';
    public $copropietario_entrega_fest_id = '';
    public $cantidad_acompanantes_permitidos = 0;
    public $confirmado = false;

    // Listas para los selects
    public $prospectos = [];
    public $copropietarios = [];

    protected function rules()
    {
        $rules = [
            'tipo_invitado' => 'required|in:titular,copropietario',
            'cantidad_acompanantes_permitidos' => 'required|integer|min:0',
            'confirmado' => 'boolean',
        ];

        if ($this->tipo_invitado === 'titular') {
            $rules['prospecto_entrega_fest_id'] = 'required|exists:prospecto_entrega_fests,id';
        } else {
            $rules['copropietario_entrega_fest_id'] = 'required|exists:copropietario_entrega_fests,id';
        }

        return $rules;
    }

    protected function validationAttributes()
    {
        return [
            'tipo_invitado' => 'tipo de invitado',
            'prospecto_entrega_fest_id' => 'prospecto titular',
            'copropietario_entrega_fest_id' => 'copropietario',
            'cantidad_acompanantes_permitidos' => 'cantidad de acompañantes',
            'confirmado' => 'confirmación',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Al cambiar el tipo, recargamos la lista correspondiente
        if ($propertyName === 'tipo_invitado') {
            $this->reset(['prospecto_entrega_fest_id', 'copropietario_entrega_fest_id']);
            if ($this->tipo_invitado === 'copropietario') {
                $this->loadCopropietarios();
            }
        }
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->loadProspectos();
    }

    public function loadProspectos()
    {
        // Prospectos titulares del evento que aún NO tienen invitación
        $this->prospectos = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->whereDoesntHave('invitado')
            ->orderBy('nombres')
            ->get();
    }

    public function loadCopropietarios()
    {
        // Copropietarios del evento que aún NO tienen invitación
        $this->copropietarios = CopropietarioEntregaFest::whereHas('prospecto', function ($q) {
            $q->where('entrega_fest_id', $this->evento->id);
        })
            ->whereDoesntHave('invitado')
            ->with('prospecto:id,nombres,lote,manzana')
            ->orderBy('nombres')
            ->get();
    }

    public function store()
    {
        $this->authorize('invitado-entrega-fest.crear');

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

            $codigo = 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT)
                . '-' . strtoupper(bin2hex(random_bytes(3)));

            InvitadoEntregaFest::create([
                'entrega_fest_id' => $this->evento->id,
                'prospecto_entrega_fest_id' => $this->tipo_invitado === 'titular'
                    ? $this->prospecto_entrega_fest_id
                    : null,
                'copropietario_entrega_fest_id' => $this->tipo_invitado === 'copropietario'
                    ? $this->copropietario_entrega_fest_id
                    : null,
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
            Log::channel('entrega-fest')->error('[INVITADO CREAR] Error al generar invitación: ' . $e->getMessage(), [
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
