<?php

namespace App\Livewire\Erp\Atc\TipoSolicitud;

use App\Models\FlujoPaso;
use App\Models\TipoSolicitud;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Flujo de Procesos')]
class TipoSolicitudFlujo extends Component
{
    public TipoSolicitud $tipoSolicitud;
    
    // Propiedades para el formulario de nuevo/editar paso
    public $nombre_paso = '';
    public $orden = 1;
    public $descripcion = '';
    public $editando_id = null;

    public function mount($id)
    {
        $this->tipoSolicitud = TipoSolicitud::findOrFail($id);
        $this->orden = ($this->tipoSolicitud->flujoPasos()->max('orden') ?? 0) + 1;
    }

    public function guardarPaso()
    {
        $this->validate([
            'nombre_paso' => 'required|string|max:255',
            'orden' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
        ]);

        if ($this->editando_id) {
            $paso = FlujoPaso::findOrFail($this->editando_id);
            $paso->update([
                'nombre_paso' => $this->nombre_paso,
                'orden' => $this->orden,
                'descripcion' => $this->descripcion,
            ]);
        } else {
            $this->tipoSolicitud->flujoPasos()->create([
                'nombre_paso' => $this->nombre_paso,
                'orden' => $this->orden,
                'descripcion' => $this->descripcion,
            ]);
        }

        $this->resetForm();
        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Guardado',
            'text' => 'El paso se ha guardado correctamente.',
        ]);
    }

    public function editarPaso($id)
    {
        $paso = FlujoPaso::findOrFail($id);
        $this->editando_id = $paso->id;
        $this->nombre_paso = $paso->nombre_paso;
        $this->orden = $paso->orden;
        $this->descripcion = $paso->descripcion;
    }

    public function eliminarPaso($id)
    {
        FlujoPaso::destroy($id);
        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Eliminado',
            'text' => 'El paso ha sido eliminado.',
        ]);
    }

    public function resetForm()
    {
        $this->reset(['nombre_paso', 'descripcion', 'editando_id']);
        $this->orden = ($this->tipoSolicitud->flujoPasos()->max('orden') ?? 0) + 1;
    }

    public function render()
    {
        return view('livewire.erp.atc.tipo-solicitud.tipo-solicitud-flujo', [
            'pasos' => $this->tipoSolicitud->flujoPasos()->orderBy('orden')->get()
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
