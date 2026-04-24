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
#[Title('Editar Avance de Proyecto')]
class AvanceProyectoEditar extends Component
{
    use WithFileUploads;

    public $item_id;
    public $unidad_negocio_id;
    public $grupo_proyecto_id;
    public $proyecto_id;
    public $titulo;
    public $descripcion;
    public $video_id;
    public $orden = 0;
    public $activo = true;
    public $imagen;
    public $imagenActual;
    public $avance_model;

    public function mount($id)
    {
        $this->avance_model = AvanceProyecto::with('miniatura')->findOrFail($id);
        $this->item_id = $this->avance_model->id;
        $this->unidad_negocio_id = $this->avance_model->unidad_negocio_id;
        $this->grupo_proyecto_id = $this->avance_model->grupo_proyecto_id;
        $this->proyecto_id = $this->avance_model->proyecto_id;
        $this->titulo = $this->avance_model->titulo;
        $this->descripcion = $this->avance_model->descripcion;
        $this->video_id = $this->avance_model->video_id;
        $this->orden = $this->avance_model->orden;
        $this->activo = $this->avance_model->activo;

        $this->imagenActual = $this->avance_model->miniatura->url ?? null;
    }

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
            'imagen' => 'nullable|image|max:1024',
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

    public function update()
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

            $this->avance_model->update([
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
                if ($this->avance_model->miniatura) {
                    Storage::disk('public')->delete($this->avance_model->miniatura->path);
                    $this->avance_model->miniatura->delete();
                }

                $path = $this->imagen->store('marketing/avance-proyectos', 'public');
                $url = Storage::url($path);

                MarketingArchivo::create([
                    'archivable_id' => $this->avance_model->id,
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
                'title' => '¡Actualizado!',
                'text' => 'Avance de proyecto actualizado correctamente.'
            ]);

            $this->avance_model->load('miniatura');
            $this->imagenActual = $this->avance_model->miniatura->url ?? null;
            $this->imagen = null;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('avance_proyecto')->error("[AVANCE PROYECTO] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'item_id' => $this->item_id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el avance de proyecto.'
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

        return view('livewire.erp.marketing.avance-proyecto.avance-proyecto-editar', [
            'unidades' => $unidades,
            'grupos' => $grupos,
            'proyectos' => $proyectos,
        ]);
    }
}
