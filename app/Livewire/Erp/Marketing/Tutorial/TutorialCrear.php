<?php

namespace App\Livewire\Erp\Marketing\Tutorial;

use App\Models\MarketingArchivo;
use App\Models\Tutorial;
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
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Tutorial')]
class TutorialCrear extends Component
{
    use WithFileUploads;

    public $titulo;
    public $descripcion;
    public $video_id;
    public $orden = 0;
    public $activo = true;
    public $imagen;

    protected function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'video_id' => 'required|string|max:50',
            'orden' => 'required|integer',
            'activo' => 'boolean',
            'imagen' => 'nullable|image|max:1024',
        ];
    }

    protected $validationAttributes = [
        'titulo' => 'título',
        'descripcion' => 'descripción',
        'video_id' => 'ID de video',
        'orden' => 'orden',
        'imagen' => 'miniatura',
    ];

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function store()
    {
        $this->authorize('tutorial.crear');

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

            $tutorial = Tutorial::create([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'video_id' => $this->video_id,
                'orden' => $this->orden,
                'activo' => $this->activo,
            ]);

            if ($this->imagen) {
                $path = $this->imagen->store('marketing/tutoriales', 'public');
                $url = Storage::url($path);

                MarketingArchivo::create([
                    'archivable_id' => $tutorial->id,
                    'archivable_type' => Tutorial::class,
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
                'text' => 'Tutorial creado correctamente.'
            ]);

            return redirect()->route('erp.tutorial.vista.editar', $tutorial->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('marketing')->error("[TUTORIAL] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el tutorial.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.marketing.tutorial.tutorial-crear');
    }
}
