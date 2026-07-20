<?php

namespace App\Livewire\Erp\Marketing\TipoClienteDocumento;

use App\Models\TipoClienteDocumento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Tipo de Documento')]
class TipoClienteDocumentoCrear extends Component
{
    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('nullable|string')]
    public $descripcion = '';

    #[Validate('nullable|integer')]
    public $orden = 0;

    #[Validate('nullable|string')]
    public $color = '#3b82f6'; // default blue

    #[Validate('nullable|string|max:255')]
    public $icono = 'fa-solid fa-file';

    #[Validate('nullable|string|max:255')]
    public $icono_documentos = '';

    public $activo = true;

    public function mount()
    {
        // Initialization if needed
    }

    public function guardar()
    {
        $this->validate();

        try {
            TipoClienteDocumento::create([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'orden' => $this->orden,
                'color' => $this->color,
                'icono' => $this->icono,
                'icono_documentos' => $this->icono_documentos,
                'activo' => $this->activo,
            ]);

            session()->flash('success', 'Tipo de documento creado exitosamente.');
            return redirect()->route('erp.tipo-cliente-documento.vista.todo');

        } catch (\Exception $e) {
            Log::channel('marketing')->error("Error al crear TipoClienteDocumento: " . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo guardar la información.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.marketing.tipo-cliente-documento.tipo-cliente-documento-crear');
    }
}
