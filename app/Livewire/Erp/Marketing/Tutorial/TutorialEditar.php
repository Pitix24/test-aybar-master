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
#[Title('Editar Tutorial')]
class TutorialEditar extends Component
{
    use WithFileUploads;

    public Tutorial $tutorial;

    public $titulo;
    public $descripcion;
    public $video_id;
    public $orden;
    public $activo;
    public $imagen;
    public $imagenActual;

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

    public function mount(Tutorial $tutorial)
    {
        $this->tutorial = $tutorial;
        $this->titulo = $tutorial->titulo;
        $this->descripcion = $tutorial->descripcion;
        $this->video_id = $tutorial->video_id;
        $this->orden = $tutorial->orden;
        $this->activo = (bool) $tutorial->activo;

        $miniatura = $tutorial->miniatura;
        if ($miniatura) {
            $this->imagenActual = $miniatura->url ?? asset($miniatura->path);
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
        $this->authorize('tutorial.editar');

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

            $this->tutorial->update([
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'video_id' => $this->video_id,
                'orden' => $this->orden,
                'activo' => $this->activo,
            ]);

            if ($this->imagen) {
                // Eliminar anterior si existe
                if ($this->tutorial->miniatura) {
                    Storage::disk('public')->delete($this->tutorial->miniatura->path);
                    $this->tutorial->miniatura->delete();
                }

                $path = $this->imagen->store('marketing/tutoriales', 'public');
                $url = Storage::url($path);

                MarketingArchivo::create([
                    'archivable_id' => $this->tutorial->id,
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
                'title' => 'Actualizado',
                'text' => 'Tutorial actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('marketing')->error("[TUTORIAL] Error en update: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al actualizar el tutorial.'
            ]);
        }
    }

    public function destroy()
    {
        $this->authorize('tutorial.eliminar');

        try {
            DB::beginTransaction();

            if ($this->tutorial->miniatura) {
                Storage::disk('public')->delete($this->tutorial->miniatura->path);
                $this->tutorial->miniatura->delete();
            }

            $this->tutorial->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Éxito',
                'text' => 'Tutorial eliminado correctamente.'
            ]);

            return redirect()->route('erp.tutorial.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('marketing')->error("[TUTORIAL] Error en destroy: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'tutorial_id' => $this->tutorial->id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al eliminar el tutorial.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.marketing.tutorial.tutorial-editar');
    }
}
