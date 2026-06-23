<?php

namespace App\Livewire\Erp\EntregaFest\ProspectoHistorico;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Imports\ProspectoHistoricoImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ProspectoHistoricoImportar extends Component
{
    use WithFileUploads;

    public $archivo;
    public $resumen = null;
    public $erroresImportacion = [];

    public function mount()
    {
        // Validación de permisos (Debe coincidir con los permisos que crearemos en la fase 4)
        abort_if(!auth()->user()->can('prospecto-historico.importar'), 403, 'No tienes permiso para importar el histórico.');
    }

    public function importarHistorico()
    {
        $this->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            $importador = new ProspectoHistoricoImport();
            Excel::import($importador, $this->archivo);

            DB::commit();

            $this->resumen = [
                'nuevos' => $importador->nuevos,
                'actualizados' => $importador->actualizados,
                'errores' => $importador->errores
            ];
            $this->erroresImportacion = $importador->detalleErrores;

            $this->reset('archivo');

            $this->dispatch('notificacion', [
                'icon' => 'success',
                'title' => 'Importación Completada',
                'text' => 'Se procesó el histórico correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notificacion', [
                'icon' => 'error',
                'title' => 'Error de importación',
                'text' => 'Ocurrió un error crítico: ' . $e->getMessage()
            ]);
        }
    }

    public function descargarPlantilla()
    {
        $path = public_path('templates/formato_importar_prospecto_historico.xlsx');
        if (file_exists($path)) {
            return response()->download($path);
        }

        $this->dispatch('notificacion', [
            'icon' => 'error',
            'title' => 'No encontrado',
            'text' => 'El archivo de plantilla no existe en el servidor.'
        ]);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.prospecto-historico.prospecto-historico-importar');
    }
}
