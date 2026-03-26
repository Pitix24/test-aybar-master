<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntregaFestMensaje extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $plantillas_data = [];
    public $pre_invitacion_file;
    public $confirmacion_file;
    public $asistencia_confirmacion_file;

    public function mount(EntregaFest $evento)
    {
        $this->evento = $evento;
        $this->cargarPlantillas();
    }

    public function cargarPlantillas()
    {
        foreach (['pre-invitacion', 'confirmacion', 'asistencia-confirmacion'] as $tipo) {
            $p = $this->evento->plantillas()->where('tipo', $tipo)->first();
            $this->plantillas_data[$tipo] = [
                'id' => $p?->id,
                'titulo' => $p?->titulo ?? '',
                'subtitulo' => $p?->subtitulo ?? '',
                'descripcion' => $p?->descripcion ?? '',
                'link_boton' => $p?->link_boton ?? '',
                'imagen_url' => $p?->getFirstMediaUrl('imagen'),
            ];
        }
    }

    public function guardarPlantilla($tipo)
    {
        $data = $this->plantillas_data[$tipo];

        try {
            DB::beginTransaction();

            $plantilla = \App\Models\EntregaFestPlantilla::updateOrCreate(
                ['entrega_fest_id' => $this->evento->id, 'tipo' => $tipo],
                [
                    'titulo' => $data['titulo'],
                    'subtitulo' => $data['subtitulo'],
                    'descripcion' => $data['descripcion'],
                    'link_boton' => $data['link_boton'],
                ]
            );

            // Archivo temporal
            $fileVar = str_replace('-', '_', $tipo) . '_file';
            if ($this->$fileVar) {
                $plantilla->clearMediaCollection('imagen');
                $plantilla->addMedia($this->$fileVar->getRealPath())
                    ->usingFileName($this->$fileVar->getClientOriginalName())
                    ->toMediaCollection('imagen');

                $this->reset($fileVar);
            }

            DB::commit();
            $this->cargarPlantillas();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Guardado!',
                'text' => "Plantilla de " . ucwords($tipo) . " actualizada con éxito."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[PLANTILLA-NESTED] $tipo: " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-mensaje');
    }
}
