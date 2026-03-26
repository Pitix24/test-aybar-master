<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EntregaFestImportarMop extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $archivo_mop_tareas;

    public function mount(EntregaFest $evento)
    {
        $this->evento = $evento;
    }

    public function descargarPlantillaMopTareas()
    {
        return response()->download(public_path('templates/formato_importar_mop_tareas_prospectos_entrega_fest.xlsx'));
    }

    public function importarMopTareas()
    {
        if (!$this->archivo_mop_tareas) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'Debe seleccionar un archivo Excel.']);
            return;
        }

        try {
            DB::beginTransaction();

            $import = new \App\Imports\EntregaFest\EntregaFestMopTareasImport($this->evento->id);
            Excel::import($import, $this->archivo_mop_tareas->getRealPath());

            DB::commit();
            $this->reset('archivo_mop_tareas');

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Tareas Asignadas!',
                'text' => 'El plan operativo MOP ha sido cargado con éxito para este evento.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.panel', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[MOP-IMPORT] : " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-importar-mop');
    }
}
