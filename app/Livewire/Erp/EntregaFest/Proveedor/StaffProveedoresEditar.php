<?php

namespace App\Livewire\Erp\EntregaFest\Proveedor;

use App\Models\EntregaFest;
use App\Models\EntregaFestProveedor;
use App\Models\EntregaFestProveedorRequerimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Proveedor - Entrega Fest')]
class StaffProveedoresEditar extends Component
{
    public EntregaFest $evento;
    public EntregaFestProveedor $proveedor;

    public $nombre_comercial = '';
    public $contacto_nombre = '';
    public $contacto_telefono = '';
    public $servicio_tipo = '';
    public $h_llegada;
    public $h_montaje;
    public $h_show;
    public $h_desmontaje;
    public $estado = 'CONFIRMADO';

    public $requerimientos = []; // List of objects (id and text)

    public function mount($id, $proveedorId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->proveedor = EntregaFestProveedor::with('requerimientos')->findOrFail($proveedorId);

        $this->nombre_comercial = $this->proveedor->nombre_comercial;
        $this->contacto_nombre = $this->proveedor->contacto_nombre;
        $this->contacto_telefono = $this->proveedor->contacto_telefono;
        $this->servicio_tipo = $this->proveedor->servicio_tipo;
        $this->h_llegada = $this->proveedor->h_llegada;
        $this->h_montaje = $this->proveedor->h_montaje;
        $this->h_show = $this->proveedor->h_show;
        $this->h_desmontaje = $this->proveedor->h_desmontaje;
        $this->estado = $this->proveedor->estado;

        foreach ($this->proveedor->requerimientos as $req) {
            $this->requerimientos[] = [
                'id' => $req->id,
                'texto' => $req->requerimiento,
                'esta_cubierto' => $req->esta_cubierto
            ];
        }
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
            'requerimientos.*.texto' => 'nullable|string|max:255',
        ];
    }

    public function agregarRequerimiento()
    {
        $this->requerimientos[] = [
            'id' => null,
            'texto' => '',
            'esta_cubierto' => false
        ];
    }

    public function removerRequerimiento($index)
    {
        $req = $this->requerimientos[$index];
        if ($req['id']) {
            EntregaFestProveedorRequerimiento::destroy($req['id']);
        }
        unset($this->requerimientos[$index]);
        $this->requerimientos = array_values($this->requerimientos);
    }

    public function update()
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

            $this->proveedor->update([
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

            // Sync requirements
            foreach ($this->requerimientos as $req) {
                if (trim($req['texto'])) {
                    if ($req['id']) {
                        EntregaFestProveedorRequerimiento::where('id', $req['id'])->update([
                            'requerimiento' => trim($req['texto'])
                        ]);
                    } else {
                        $this->proveedor->requerimientos()->create([
                            'requerimiento' => trim($req['texto']),
                            'esta_cubierto' => false
                        ]);
                    }
                }
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Datos del proveedor actualizados correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STAFF PROVEEDOR EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el proveedor.'
            ]);
        }
    }

    #[On('eliminarProveedorOn')]
    public function eliminarProveedorOn()
    {
        try {
            DB::beginTransaction();
            $this->proveedor->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Proveedor eliminado del evento.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.staff.proveedores', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STAFF PROVEEDOR ELIMINAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el proveedor.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.proveedor.staff-proveedores-editar');
    }
}
