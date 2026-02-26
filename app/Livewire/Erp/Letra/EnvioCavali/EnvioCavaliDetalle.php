<?php

namespace App\Livewire\Erp\Letra\EnvioCavali;

use App\Exports\Letra\CavaliAceptanteExport;
use App\Exports\Letra\CavaliGiradorExport;
use App\Exports\Letra\CavaliLetrasExport;
use App\Models\EnvioCavali;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ValidarEnviosCavaliDiariosJob;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Detalle de Envío CAVALI')]
class EnvioCavaliDetalle extends Component
{
    public EnvioCavali $envio;

    public function mount($id)
    {
        $this->envio = EnvioCavali::with([
            'unidadNegocio',
            'solicitudes.userCliente.perfilCliente',
            'solicitudes.proyecto'
        ])->findOrFail($id);
    }

    public function descargarAceptantes()
    {
        $this->authorize('envio-cavali.exportar-envios');
        return Excel::download(new CavaliAceptanteExport($this->envio), "ACEPTANTE_{$this->envio->id}.xlsx");
    }

    public function descargarLetras()
    {
        $this->authorize('envio-cavali.exportar-envios');
        return Excel::download(new CavaliLetrasExport($this->envio), "LETRAS_{$this->envio->id}.xlsx");
    }

    public function descargarGirador()
    {
        $this->authorize('envio-cavali.exportar-envios');
        return Excel::download(new CavaliGiradorExport($this->envio), "GIRADOR_{$this->envio->id}.xlsx");
    }

    public function descargarArchivo()
    {
        $this->authorize('envio-cavali.exportar-envios');
        if (!$this->envio->archivo_zip) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No hay archivo disponible para descargar.'
            ]);
            return;
        }

        if (!Storage::disk('local')->exists($this->envio->archivo_zip)) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'El archivo no existe en el servidor.'
            ]);
            return;
        }

        return Storage::disk('local')->download($this->envio->archivo_zip, $this->envio->archivo_nombre);
    }

    public function validarCronLetra()
    {
        $this->authorize('solicitud-digitalizar-letra.validar-cron-letra');

        try {
            ValidarEnviosCavaliDiariosJob::dispatch();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Proceso Iniciado',
                'text' => 'La validación se está procesando en segundo plano. Puede seguir navegando.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo iniciar el proceso: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.letra.envio-cavali.envio-cavali-detalle');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
