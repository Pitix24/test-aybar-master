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
#[Title('Crear Documento de Cliente')]
class ClienteDocumentoCrear extends Component
{
    use WithFileUploads;

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

    #[Validate('required|file|mimes:pdf|max:10240')] // max 10MB
    public $archivo;

    public function guardar()
    {
        $this->validate();

        try {
            $documento = ClienteDocumento::create([
                'proyecto_id' => $this->proyecto_id,
                'tipo_cliente_documentos_id' => $this->tipo_cliente_documentos_id,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'icono' => $this->icono,
                'orden' => $this->orden,
                'solo_lectura' => $this->solo_lectura,
                'activo' => $this->activo,
                'clicks' => 0,
            ]);

            if ($this->archivo) {
                $path = $this->archivo->store('documentos_clientes', 'local');

                $documento->archivoPdf()->create([
                    'user_id' => auth()->id(),
                    'nombre_original' => $this->archivo->getClientOriginalName(),
                    'path' => $path,
                    'url' => Storage::url($path),
                    'extension' => $this->archivo->getClientOriginalExtension(),
                    'size' => $this->archivo->getSize(),
                    'mime_type' => $this->archivo->getMimeType(),
                ]);
            }

            session()->flash('success', 'Documento creado exitosamente.');
            return redirect()->route('erp.cliente-documento.vista.todo');
        } catch (\Exception $e) {
            Log::channel('marketing')->error("Error al crear ClienteDocumento: " . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo guardar la información.'
            ]);
        }
    }

    public function render()
    {
        $proyectos = Proyecto::where('activo', true)->orderBy('nombre')->get();
        $tipos = TipoClienteDocumento::where('activo', true)->orderBy('orden')->get();

        return view('livewire.erp.marketing.cliente-documento.cliente-documento-crear', [
            'proyectos' => $proyectos,
            'tipos' => $tipos,
        ]);
    }
}
