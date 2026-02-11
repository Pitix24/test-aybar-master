<?php

namespace App\Livewire\Letra\EnvioCavaliSolicitud;

use App\Exports\CavaliAceptanteExport;
use App\Exports\CavaliGiradorExport;
use App\Exports\CavaliLetrasExport;
use App\Models\EnvioCavali;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Envío CAVALI')]
class EnvioCavaliSolicitudEditar extends Component
{
    public EnvioCavali $envio;

    public function mount($id)
    {
        $this->envio = EnvioCavali::with([
            'solicitudes.unidadNegocio',
            'solicitudes.proyecto',
            'solicitudes.userCliente.perfilCliente',
            'solicitudes.userCliente.direccion.distrito',
        ])->findOrFail($id);
    }

    public function descargarAceptantes()
    {
        return Excel::download(
            new CavaliAceptanteExport($this->envio),
            "ACEPTANTE_{$this->envio->id}.xlsx"
        );
    }

    public function descargarLetras()
    {
        return Excel::download(
            new CavaliLetrasExport($this->envio),
            "LETRAS_{$this->envio->id}.xlsx"
        );
    }

    public function descargarGirador()
    {
        return Excel::download(
            new CavaliGiradorExport($this->envio),
            "GIRADOR_{$this->envio->id}.xlsx"
        );
    }

    public function descargarArchivo()
    {
        if (!$this->envio->archivo_zip) {
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No hay archivo disponible para descargar.']);
            return;
        }

        if (!Storage::disk('public')->exists($this->envio->archivo_zip)) {
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'El archivo no existe en el servidor.']);
            return;
        }

        $path = Storage::disk('public')->path($this->envio->archivo_zip);
        return response()->download($path, $this->envio->archivo_nombre);
    }

    public function render()
    {
        return view('livewire.letra.envio-cavali-solicitud.envio-cavali-solicitud-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
