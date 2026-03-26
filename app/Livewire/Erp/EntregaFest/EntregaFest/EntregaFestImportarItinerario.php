<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EntregaFestImportarItinerario extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $archivo_itinerario;

    public function mount(EntregaFest $evento)
    {
        $this->evento = $evento;
    }

    public function descargarPlantillaItinerario()
    {
        return response()->download(public_path('templates/formato_importar_itinerario_prospectos_entrega_fest.xlsx'));
    }

    public function importarItinerario()
    {
        if (!$this->archivo_itinerario) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'Debe seleccionar un archivo Excel.']);
            return;
        }

        try {
            DB::beginTransaction();

            $import = new \App\Imports\EntregaFest\EntregaFestItinerarioImport($this->evento->id);
            Excel::import($import, $this->archivo_itinerario->getRealPath());

            DB::commit();
            $this->reset('archivo_itinerario');

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Cronograma Cargado!',
                'text' => 'El itinerario de este evento ya está en la plataforma.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.panel', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[ITINERARIO-IMPORT] : " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-importar-itinerario');
    }
}
