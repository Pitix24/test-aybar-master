<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Registrar Prospecto - Entrega Fest')]
class EntregaFestProspectoCrear extends Component
{
    public EntregaFest $evento;

    // Campos del prospecto (Simplificado para registro inicial)
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '';
    public $lote = '', $manzana = '';

    public $proyectos = [];

    protected function rules()
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'proyecto_id' => 'proyecto',
            'dni' => 'DNI',
            'nombres' => 'nombres completos',
            'email' => 'correo electrónico',
            'celular' => 'número de celular',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
    }

    public function store()
    {
        $this->authorize('prospecto.crear');

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

        // Verificar duplicado en este evento
        $existe = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->where('dni', $this->dni)
            ->exists();

        if ($existe) {
            $this->addError('dni', 'Esta persona ya está registrada para este evento.');
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'DNI Duplicado',
                'text' => 'El prospecto con DNI ' . $this->dni . ' ya existe en este festival.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            ProspectoEntregaFest::create([
                'entrega_fest_id' => $this->evento->id,
                'proyecto_id' => $this->proyecto_id,
                'user_id' => Auth::id(),
                'dni' => $this->dni,
                'nombres' => trim($this->nombres),
                'email' => trim($this->email),
                'celular' => trim($this->celular),
                'lote' => $this->lote,
                'manzana' => $this->manzana,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Prospecto ' . $this->nombres . ' registrado correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.prospectos', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[PROSPECTO CREAR] Error al registrar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar el prospecto.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
