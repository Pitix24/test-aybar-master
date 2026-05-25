<?php

namespace App\Livewire\Erp\Soporte;

use App\Models\Erp\Soporte\Soporte;
use App\Models\Erp\Soporte\SoporteArchivo as SoporteArchivoModel;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class SoporteArchivo extends Component
{
    use WithFileUploads;

    public Soporte $soporte;
    public $archivo;
    public $descripcion_archivo;
    public $archivos_existentes = [];
    public $soloLectura = false;

    public function mount(Soporte $soporte, $soloLectura = false)
    {
        $this->soporte = $soporte;
        $this->soloLectura = $soloLectura;
        $this->refreshArchivos();
    }

    public function refreshArchivos()
    {
        $this->archivos_existentes = $this->soporte->archivos()->get();
    }

    public function adjuntar()
    {
        $this->authorize('soporte.accion-agregar-archivo');

        try {
            $this->validate([
                'archivo' => 'required|file|max:51200|mimes:pdf,docx,xlsx,pptx,jpg,jpeg,png',
                'descripcion_archivo' => 'required|min:3|max:200',
            ]);
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

            $filename = $this->archivo->getClientOriginalName();
            $extension = $this->archivo->getClientOriginalExtension();
            $path = $this->archivo->store('soportes/' . $this->soporte->id, 'public');

            SoporteArchivoModel::create([
                'archivable_type' => Soporte::class,
                'archivable_id' => $this->soporte->id,
                'user_id' => auth()->id(),
                'nombre_original' => $filename,
                'path' => $path,
                'url' => Storage::url($path),
                'descripcion' => $this->descripcion_archivo,
                'extension' => $extension,
                'size' => $this->archivo->getSize(),
                'mime_type' => $this->archivo->getMimeType(),
            ]);

            DB::commit();

            $this->reset(['archivo', 'descripcion_archivo']);
            $this->refreshArchivos();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Adjunto',
                'text' => 'Archivo subido con éxito.'
            ]);
            $this->dispatch('archivoSubido');
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('soporte')->error('[SOPORTE] Error SoporteArchivo@adjuntar: ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al subir el archivo.'
            ]);
        }
    }

    public function eliminarArchivo($archivoId)
    {
        $this->authorize('soporte.accion-eliminar-archivo');

        try {
            $archivo = SoporteArchivoModel::findOrFail($archivoId);

            if ($archivo->archivable_id !== $this->soporte->id) {
                throw new Exception('Archivo no pertenece a este soporte.');
            }

            // Eliminar del almacenamiento
            if (Storage::disk('public')->exists($archivo->path)) {
                Storage::disk('public')->delete($archivo->path);
            }

            // Soft delete del registro
            $archivo->delete();

            $this->refreshArchivos();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'Archivo eliminado correctamente.'
            ]);
        } catch (Exception $e) {
            Log::channel('soporte')->error('[SOPORTE] Error SoporteArchivo@eliminarArchivo: ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al eliminar el archivo.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.soporte.soporte-archivo');
    }
}
