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
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Tutorial')]
class TutorialEditar extends Component
{
    use WithFileUploads;

    public Tutorial $tutorial_model;

    public $titulo;
    public $descripcion;
    public $video_id;
    public $orden;
    public $activo;
    public $imagen;
    public $imagenActual;

    public function mount($id)
    {
        $this->tutorial_model = Tutorial::findOrFail($id);

        $this->titulo = $this->tutorial_model->titulo;
        $this->descripcion = $this->tutorial_model->descripcion;
        $this->video_id = $this->tutorial_model->video_id;
        $this->orden = $this->tutorial_model->orden;
        $this->activo = (bool) $this->tutorial_model->activo;

        $miniatura = $this->tutorial_model->miniatura;
        if ($miniatura) {
            $this->imagenActual = $miniatura->url ?? asset($miniatura->path);
        }
    }

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

    protected function validationAttributes()
    {
        return [
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'video_id' => 'ID de video',
            'orden' => 'orden',
            'imagen' => 'miniatura',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
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

            $this->tutorial_model->update([
                'titulo' => trim($this->titulo),
                'descripcion' => trim($this->descripcion),
                'video_id' => trim($this->video_id),
                'orden' => $this->orden,
                'activo' => $this->activo,
            ]);

            if ($this->imagen) {
                if ($this->tutorial_model->miniatura) {
                    Storage::disk('public')->delete($this->tutorial_model->miniatura->path);
                    $this->tutorial_model->miniatura->delete();
                }

                $path = $this->imagen->store('marketing/tutoriales', 'public');
                $url = Storage::url($path);

                MarketingArchivo::create([
                    'archivable_id' => $this->tutorial_model->id,
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
                'title' => '¡Actualizado!',
                'text' => 'Tutorial actualizado correctamente.'
            ]);

            $this->tutorial_model->load('miniatura');
            $this->imagenActual = $this->tutorial_model->miniatura->url ?? asset($this->tutorial_model->miniatura->path);
            $this->imagen = null;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tutorial')->error("[TUTORIAL] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->tutorial_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el tutorial.'
            ]);
        }
    }

    #[On('eliminarTutorialOn')]
    public function eliminarTutorialOn()
    {
        $this->authorize('tutorial.eliminar');

        try {
            DB::beginTransaction();

            if ($this->tutorial_model->miniatura) {
                Storage::disk('public')->delete($this->tutorial_model->miniatura->path);
                $this->tutorial_model->miniatura->delete();
            }

            $this->tutorial_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Tutorial eliminado correctamente.'
            ]);

            return redirect()->route('erp.tutorial.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tutorial')->error("[TUTORIAL] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->tutorial_model->id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el tutorial.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.marketing.tutorial.tutorial-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
