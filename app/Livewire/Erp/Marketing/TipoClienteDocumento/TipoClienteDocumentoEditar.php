<?php

namespace App\Livewire\Erp\Marketing\TipoClienteDocumento;

use App\Models\TipoClienteDocumento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Tipo de Documento')]
class TipoClienteDocumentoEditar extends Component
{
    public TipoClienteDocumento $tipo;

    #[Validate('required|string|max:255')]
    public $nombre = '';

    #[Validate('nullable|string')]
    public $descripcion = '';

    #[Validate('nullable|integer')]
    public $orden = 0;

    #[Validate('nullable|string')]
    public $color = '';

    #[Validate('nullable|string|max:255')]
    public $icono = '';

    #[Validate('nullable|string|max:255')]
    public $icono_documentos = '';

    public $activo = true;

    public function mount($id)
{
    // Buscamos explícitamente el registro en la base de datos
    $this->tipo = TipoClienteDocumento::findOrFail($id);

    // Ahora asignamos los valores a las propiedades públicas
    $this->nombre = $this->tipo->nombre;
    $this->descripcion = $this->tipo->descripcion;
    $this->orden = $this->tipo->orden;
    $this->color = $this->tipo->color;
    $this->icono = $this->tipo->icono;
    $this->icono_documentos = $this->tipo->icono_documentos;
    $this->activo = $this->tipo->activo;
}

    public function guardar()
    {
        $this->validate();

        try {
            $this->tipo->update([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'orden' => $this->orden,
                'color' => $this->color,
                'icono' => $this->icono,
                'icono_documentos' => $this->icono_documentos,
                'activo' => $this->activo,
            ]);

            session()->flash('success', 'Tipo de documento actualizado exitosamente.');
            return redirect()->route('erp.tipo-cliente-documento.vista.todo');

        } catch (\Exception $e) {
            Log::channel('marketing')->error("Error al actualizar TipoClienteDocumento: " . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la información.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.marketing.tipo-cliente-documento.tipo-cliente-documento-editar');
    }
}
