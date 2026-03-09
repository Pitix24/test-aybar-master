<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFestMopPlantilla;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MopPlantillaTemplateExport;
use App\Imports\MopPlantillaImport;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Plantilla MOP')]
class MopPlantillaCrear extends Component
{
    use WithFileUploads;

    public $archivo_excel;
    public $rol_nombre = '';
    public $fase = 'ANTES';
    public $instruccion = '';
    public $prioridad = 1;

    protected function rules()
    {
        return [
            'rol_nombre' => 'required|string|max:100',
            'fase' => 'required|in:ANTES,DURANTE,CIERRE',
            'instruccion' => 'required|string',
            'prioridad' => 'required|integer|min:1',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'rol_nombre' => 'rol / cargo',
            'fase' => 'fase del evento',
            'instruccion' => 'instrucción',
            'prioridad' => 'prioridad',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
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

            EntregaFestMopPlantilla::create([
                'rol_nombre' => trim($this->rol_nombre),
                'fase' => $this->fase,
                'instruccion' => trim($this->instruccion),
                'prioridad' => $this->prioridad,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'Plantilla MOP creada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.staff.mop.plantillas');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP PLANTILLA CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear la plantilla.'
            ]);
        }
    }

    public function descargarPlantilla()
    {
        return Excel::download(new MopPlantillaTemplateExport, 'plantilla_mop_global.xlsx');
    }

    public function importarExcel()
    {
        $this->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new MopPlantillaImport();
            Excel::import($import, $this->archivo_excel->getRealPath());

            $this->dispatch('alertaLivewire', [
                'type' => count($import->errores) > 0 ? 'warning' : 'success',
                'title' => 'Importación Finalizada',
                'text' => "Se importaron {$import->importados} plantillas correctamente."
            ]);

            $this->reset('archivo_excel');

            if (count($import->errores) === 0) {
                return redirect()->route('erp.entrega-fest.mop-plantilla.todo');
            }

        } catch (\Exception $e) {
            Log::error('[MOP IMPORT] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Error al procesar el archivo: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.mop.mop-plantilla-crear');
    }
}
