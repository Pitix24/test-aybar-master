<?php

namespace App\Livewire\Erp\Marketing\ClienteDocumento;

use App\Models\ClienteDocumento;
use App\Models\Proyecto;
use App\Models\TipoClienteDocumento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Documento de Cliente')]
class ClienteDocumentoEditar extends Component
{
    use WithFileUploads;

    public ClienteDocumento $documento;

    #[Validate('required|integer|exists:proyectos,id')]
    public $proyecto_id = '';

    #[Validate('required|integer|exists:tipo_cliente_documentos,id')]
    public $tipo_cliente_documentos_id = '';

    #[Validate('required|string|max:255')]
    public $titulo = '';

    #[Validate('nullable|string')]
    public $descripcion = '';

    #[Validate('nullable|string|max:255')]
    public $icono = '';

    #[Validate('nullable|integer')]
    public $orden = 0;

    public $solo_lectura = true;
    public $activo = true;

    #[Validate('nullable|file|mimes:pdf|max:10240')] // max 10MB
    public $archivo_nuevo;

    public function mount(ClienteDocumento $documento)
    {
        $this->documento = $documento;
        $this->proyecto_id = $documento->proyecto_id;
        $this->tipo_cliente_documentos_id = $documento->tipo_cliente_documentos_id;
        $this->titulo = $documento->titulo;
        $this->descripcion = $documento->descripcion;
        $this->icono = $documento->icono;
        $this->orden = $documento->orden;
        $this->solo_lectura = $documento->solo_lectura;
        $this->activo = $documento->activo;
    }

    public function guardar()
    {
        $this->validate();

        try {
            $this->documento->update([
                'proyecto_id' => $this->proyecto_id,
                'tipo_cliente_documentos_id' => $this->tipo_cliente_documentos_id,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'icono' => $this->icono,
                'orden' => $this->orden,
                'solo_lectura' => $this->solo_lectura,
                'activo' => $this->activo,
            ]);

            if ($this->archivo_nuevo) {
                // Remove old file if exists
                if ($this->documento->archivoPdf) {
                    if (Storage::disk('public')->exists($this->documento->archivoPdf->path)) {
                        Storage::disk('public')->delete($this->documento->archivoPdf->path);
                    }
                    $this->documento->archivoPdf()->delete();
                }

                $path = $this->archivo_nuevo->store('documentos_clientes', 'public');

                $this->documento->archivoPdf()->create([
                    'user_id' => auth()->id(),
                    'nombre_original' => $this->archivo_nuevo->getClientOriginalName(),
                    'path' => $path,
                    'url' => Storage::url($path),
                    'extension' => $this->archivo_nuevo->getClientOriginalExtension(),
                    'size' => $this->archivo_nuevo->getSize(),
                    'mime_type' => $this->archivo_nuevo->getMimeType(),
                ]);
            }

            session()->flash('success', 'Documento actualizado exitosamente.');
            return redirect()->route('erp.cliente-documento.vista.todo');
        } catch (\Exception $e) {
            Log::channel('marketing')->error("Error al actualizar ClienteDocumento: " . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la información.'
            ]);
        }
    }

    public function render()
    {
        $proyectos = Proyecto::where('activo', true)->orderBy('nombre')->get();
        $tipos = TipoClienteDocumento::where('activo', true)->orderBy('orden')->get();

        return view('livewire.cliente-documento.cliente-documento-editar', [
            'proyectos' => $proyectos,
            'tipos' => $tipos,
        ]);
    }
}
