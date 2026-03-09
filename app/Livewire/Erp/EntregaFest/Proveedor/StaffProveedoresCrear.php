<?php

namespace App\Livewire\Erp\EntregaFest\Proveedor;

use App\Models\EntregaFest;
use App\Models\EntregaFestProveedor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Añadir Proveedor - Entrega Fest')]
class StaffProveedoresCrear extends Component
{
    public EntregaFest $evento;

    public $nombre_comercial = '';
    public $contacto_nombre = '';
    public $contacto_telefono = '';
    public $servicio_tipo = '';
    public $h_llegada;
    public $h_montaje;
    public $h_show;
    public $h_desmontaje;
    public $estado = 'CONFIRMADO';

    public $requerimientos = []; // Array of strings

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    protected function rules()
    {
        return [
            'nombre_comercial' => 'required|string|max:150',
            'contacto_nombre' => 'nullable|string|max:100',
            'contacto_telefono' => 'nullable|string|max:20',
            'servicio_tipo' => 'nullable|string|max:100',
            'h_llegada' => 'nullable',
            'h_montaje' => 'nullable',
            'h_show' => 'nullable',
            'h_desmontaje' => 'nullable',
            'estado' => 'required|in:CONFIRMADO,EN_SITIO,COMPLETADO',
            'requerimientos.*' => 'nullable|string|max:255',
        ];
    }

    public function agregarRequerimiento()
    {
        $this->requerimientos[] = '';
    }

    public function removerRequerimiento($index)
    {
        unset($this->requerimientos[$index]);
        $this->requerimientos = array_values($this->requerimientos);
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

            $proveedor = EntregaFestProveedor::create([
                'entrega_fest_id' => $this->evento->id,
                'nombre_comercial' => trim($this->nombre_comercial),
                'contacto_nombre' => trim($this->contacto_nombre),
                'contacto_telefono' => trim($this->contacto_telefono),
                'servicio_tipo' => trim($this->servicio_tipo),
                'h_llegada' => $this->h_llegada ?: null,
                'h_montaje' => $this->h_montaje ?: null,
                'h_show' => $this->h_show ?: null,
                'h_desmontaje' => $this->h_desmontaje ?: null,
                'estado' => $this->estado,
            ]);

            foreach ($this->requerimientos as $req) {
                if (trim($req)) {
                    $proveedor->requerimientos()->create([
                        'requerimiento' => trim($req),
                        'esta_cubierto' => false
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Proveedor añadido correctamente al evento.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.staff.proveedores', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STAFF PROVEEDOR CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar el proveedor.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.proveedor.staff-proveedores-crear');
    }
}
