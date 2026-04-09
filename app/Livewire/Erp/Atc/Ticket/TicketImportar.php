<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Imports\TicketImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Importar Tickets')]
class TicketImportar extends Component
{
    use WithFileUploads;

    public $archivo_excel;
    public $registrosImportados = [];
    public $erroresImportacion  = [];
    public $columnasDetectadas  = [];

    public function descargarPlantilla()
    {
        $path = public_path('templates/formato_importar_tickets.xlsx');
        if (!file_exists($path)) {
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'La plantilla no existe en el servidor.']);
            return;
        }
        return response()->download($path);
    }

    public function importarTickets()
    {
        if (!$this->archivo_excel) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'Debe seleccionar un archivo Excel.']);
            return;
        }

        $this->reset(['registrosImportados', 'erroresImportacion', 'columnasDetectadas']);

        try {
            $import = new TicketImport();
            Excel::import($import, $this->archivo_excel->getRealPath());

            $this->registrosImportados = $import->filasImportadasData;
            $this->erroresImportacion  = $import->errores;
            $this->columnasDetectadas  = $import->columnasExcel;

            $this->reset('archivo_excel');

            if ($import->importados === 0 && count($import->errores) > 0) {
                $this->dispatch('alertaLivewire', [
                    'type'              => 'error',
                    'title'             => 'Error en Importación',
                    'text'              => 'No se pudo importar ningún registro. Revisa los errores detallados abajo.',
                    'showConfirmButton'  => true,
                ]);
                return;
            }

            $type  = 'success';
            $title = '¡Importación Exitosa!';
            $text  = "Se registraron {$import->importados} tickets correctamente.";

            if (count($import->errores) > 0) {
                $type  = 'warning';
                $title = 'Importación Parcial';
                $text  = "Se registraron {$import->importados} tickets. " . count($import->errores) . " filas tuvieron errores.";
            }

            $this->dispatch('alertaLivewire', [
                'type'             => $type,
                'title'            => $title,
                'text'             => $text,
                'showConfirmButton' => true,
            ]);

        } catch (\Exception $e) {
            Log::error("[TICKET-IMPORT] : " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-importar');
    }
}
