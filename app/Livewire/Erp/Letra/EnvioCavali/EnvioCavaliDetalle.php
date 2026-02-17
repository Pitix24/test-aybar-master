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
        abort_unless(auth()->user()->can('envio-cavali-solicitud.exportar'), 403);
        return Excel::download(new CavaliAceptanteExport($this->envio), "ACEPTANTE_{$this->envio->id}.xlsx");
    }

    public function descargarLetras()
    {
        abort_unless(auth()->user()->can('envio-cavali-solicitud.exportar'), 403);
        return Excel::download(new CavaliLetrasExport($this->envio), "LETRAS_{$this->envio->id}.xlsx");
    }

    public function descargarGirador()
    {
        abort_unless(auth()->user()->can('envio-cavali-solicitud.exportar'), 403);
        return Excel::download(new CavaliGiradorExport($this->envio), "GIRADOR_{$this->envio->id}.xlsx");
    }

    public function descargarArchivo()
    {
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
