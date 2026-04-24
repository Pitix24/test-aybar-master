<?php

namespace App\Livewire\Erp\Marketing\AvanceProyecto;

use App\Models\AvanceProyecto;
use App\Models\UnidadNegocio;
use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use App\Models\MarketingArchivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Avance de Proyecto')]
class AvanceProyectoCrear extends Component
{
    use WithFileUploads;

    public $unidad_negocio_id;
    public $grupo_proyecto_id;
    public $proyecto_id;
    public $titulo;
    public $descripcion;
    public $video_id;
    public $orden = 0;
    public $activo = true;
    public $imagen;

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'grupo_proyecto_id' => 'nullable|exists:grupo_proyectos,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'video_id' => 'required|string|max:50',
            'orden' => 'required|integer',
            'activo' => 'boolean',
            'imagen' => 'required|image|max:1024',
        ];
    }

    protected $validationAttributes = [
        'unidad_negocio_id' => 'unidad de negocio',
        'grupo_proyecto_id' => 'grupo de proyecto',
        'proyecto_id' => 'proyecto',
        'titulo' => 'título',
        'descripcion' => 'descripción',
        'video_id' => 'ID de video',
        'orden' => 'orden',
        'imagen' => 'miniatura',
    ];

    public function updated($property)
    {
        if ($property == 'unidad_negocio_id') {
            $this->grupo_proyecto_id = null;
            $this->proyecto_id = null;
        }

        if ($property == 'grupo_proyecto_id') {
            $this->proyecto_id = null;
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function store()
    {
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

            $item = AvanceProyecto::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'grupo_proyecto_id' => $this->grupo_proyecto_id,
                'proyecto_id' => $this->proyecto_id,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'video_id' => $this->video_id,
                'orden' => $this->orden,
                'activo' => $this->activo,
            ]);

            if ($this->imagen) {
                $path = $this->imagen->store('marketing/avance-proyectos', 'public');
                $url = Storage::url($path);

                MarketingArchivo::create([
                    'archivable_id' => $item->id,
                    'archivable_type' => AvanceProyecto::class,
                    'user_id' => auth()->id(),
                    'nombre_original' => $this->imagen->getClientOriginalName(),
                    'path' => $path,
                    'url' => $url,
                    'extension' => $this->imagen->getClientOriginalExtension(),
                    'size' => $this->imagen->getSize(),
                    'mime_type' => $this->imagen->getMimeType(),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'Avance de proyecto creado correctamente.'
            ]);

            return redirect()->route('erp.avance-proyecto.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('avance_proyecto')->error("[AVANCE PROYECTO] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el avance de proyecto.'
            ]);
        }
    }

    public function render()
    {
        $unidades = UnidadNegocio::where('activo', true)->get();
        $grupos = GrupoProyecto::where('activo', true)->get();

        $proyectos = Proyecto::query()
            ->where('activo', true)
            ->when($this->unidad_negocio_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->grupo_proyecto_id, function ($query) {
                $query->where('grupo_proyecto_id', $this->grupo_proyecto_id);
            })
            ->get();

        return view('livewire.erp.marketing.avance-proyecto.avance-proyecto-crear', [
            'unidades' => $unidades,
            'grupos' => $grupos,
            'proyectos' => $proyectos,
        ]);
    }
}
