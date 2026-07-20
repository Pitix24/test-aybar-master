<?php

namespace App\Livewire\Erp\Marketing\Reglamento;

use App\Models\Reglamento;
use App\Models\Proyecto;
use App\Models\MarketingArchivo;
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
#[Title('Editar Reglamento')]
class ReglamentoEditar extends Component
{
    use WithFileUploads;

    public Reglamento $reglamento_model;

    public $proyecto_id;
    public $titulo;
    public $descripcion;
    public $orden;
    public $activo;
    public $archivo;
    public $archivoActual;

    public function mount($id)
    {
        $this->reglamento_model = Reglamento::with('archivoPdf')->findOrFail($id);

        $this->proyecto_id = $this->reglamento_model->proyecto_id;
        $this->titulo = $this->reglamento_model->titulo;
        $this->descripcion = $this->reglamento_model->descripcion;
        $this->orden = $this->reglamento_model->orden;
        $this->activo = (bool) $this->reglamento_model->activo;

        $pdf = $this->reglamento_model->archivoPdf;
        if ($pdf) {
            // Usamos la URL de stream guardada en BD, o la generamos si falta (compatibilidad hacia atrás)
            $this->archivoActual = $pdf->url ?? route('cliente.reglamento.stream', ['id' => $this->reglamento_model->id]);
        }
    }

    protected function rules()
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'required|integer',
            'activo' => 'boolean',
            'archivo' => 'nullable|file|mimes:pdf|max:51200',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'proyecto_id' => 'proyecto',
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'orden' => 'orden',
            'archivo' => 'archivo PDF',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('reglamento.editar');

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

            $this->reglamento_model->update([
                'proyecto_id' => $this->proyecto_id,
                'titulo' => trim($this->titulo),
                'descripcion' => trim($this->descripcion),
                'orden' => $this->orden,
                'activo' => $this->activo,
            ]);

            if ($this->archivo) {
                if ($this->reglamento_model->archivoPdf) {
                    // CAMBIO: Eliminar del disco privado 'local'
                    Storage::disk('local')->delete($this->reglamento_model->archivoPdf->path);
                    $this->reglamento_model->archivoPdf->delete();
                }

                // Y al guardar el nuevo archivo dentro de ReglamentoEditar, repites la misma lógica del Paso 1:
                $path = $this->archivo->store('marketing/reglamentos', 'local');
                $url = route('cliente.reglamento.stream', ['id' => $this->reglamento_model->id]);

                MarketingArchivo::create([
                    'archivable_id' => $this->reglamento_model->id,
                    'archivable_type' => Reglamento::class,
                    'user_id' => auth()->id(),
                    'nombre_original' => $this->archivo->getClientOriginalName(),
                    'path' => $path,
                    'url' => $url,
                    'extension' => $this->archivo->getClientOriginalExtension(),
                    'size' => $this->archivo->getSize(),
                    'mime_type' => $this->archivo->getMimeType(),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Reglamento actualizado correctamente.'
            ]);

            $this->reglamento_model->load('archivoPdf');
            $this->archivoActual = $this->reglamento_model->archivoPdf->url ?? route('cliente.reglamento.stream', ['id' => $this->reglamento_model->id]);
            $this->archivo = null;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('reglamento')->error("[REGLAMENTO] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->reglamento_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el reglamento.'
            ]);
        }
    }

    #[On('eliminarReglamentoOn')]
    public function eliminarReglamentoOn()
    {
        $this->authorize('reglamento.eliminar');

        try {
            DB::beginTransaction();

            if ($this->reglamento_model->archivoPdf) {
                // CORRECCIÓN: Eliminar del disco 'local' (privado), no del 'public'
                Storage::disk('local')->delete($this->reglamento_model->archivoPdf->path);
                $this->reglamento_model->archivoPdf->delete();
            }

            $this->reglamento_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Reglamento eliminado correctamente.'
            ]);

            return redirect()->route('erp.reglamento.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('reglamento')->error("[REGLAMENTO] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->reglamento_model->id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el reglamento.'
            ]);
        }
    }

    public function render()
    {
        $proyectos = Proyecto::where('activo', true)->get();

        return view('livewire.erp.marketing.reglamento.reglamento-editar', [
            'proyectos' => $proyectos,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
