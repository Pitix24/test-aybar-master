<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Imports\ProspectoEntregaFestImport;
use App\Models\EntregaFest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EntregaFestImportarProspecto extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $archivo_excel;

    public function mount(EntregaFest $evento)
    {
        $this->evento = $evento;
    }

    public function descargarPlantilla()
    {
        return response()->download(public_path('templates/formato_importar_prospectos_entrega_fest.xlsx'));
    }

    public function importarProspectos()
    {
        if (!$this->archivo_excel) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'Debe seleccionar un archivo Excel.']);
            return;
        }

        $proyectosValidos = collect($this->evento->proyectos)->pluck('id')->toArray();

        try {
            DB::beginTransaction();

            $import = new ProspectoEntregaFestImport($this->evento->id, $proyectosValidos);
            Excel::import($import, $this->archivo_excel->getRealPath());

            DB::commit();
            $this->reset('archivo_excel');

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Prospectos importados correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[PROSPECTO-IMPORT] : " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-importar-prospecto');
    }
}
