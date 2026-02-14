<?php

namespace App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra;

use App\Models\EstadoSolicitudDigitalizarLetra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Estado de Solicitud de Digitalización de Letra')]
class EstadoSolicitudDigitalizarLetraEditar extends Component
{
    public EstadoSolicitudDigitalizarLetra $estadoSolicitudDigitalizarLetra;

    public $nombre;
    public $color;
    public $icono;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:estado_solicitud_digitalizar_letras,nombre,' . $this->estadoSolicitudDigitalizarLetra->id,
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->estadoSolicitudDigitalizarLetra = EstadoSolicitudDigitalizarLetra::findOrFail($id);

        $this->nombre = $this->estadoSolicitudDigitalizarLetra->nombre;
        $this->color = $this->estadoSolicitudDigitalizarLetra->color;
        $this->icono = $this->estadoSolicitudDigitalizarLetra->icono;
        $this->activo = $this->estadoSolicitudDigitalizarLetra->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        abort_unless(auth()->user()->can('estado-solicitud-digitalizar-letra.editar'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->estadoSolicitudDigitalizarLetra->update([
                'nombre' => $this->nombre,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar estado de solicitud de digitalización de letra: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarEstadoSolicitudDigitalizarLetraOn')]
    public function eliminarEstadoSolicitudDigitalizarLetraOn()
    {
        abort_unless(auth()->user()->can('estado-solicitud-digitalizar-letra.eliminar'), 403);
        try {
            DB::beginTransaction();

            $this->estadoSolicitudDigitalizarLetra->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.estado-solicitud-digitalizar-letra.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar estado de solicitud de digitalización de letra: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.letra.estado-solicitud-digitalizar-letra.estado-solicitud-digitalizar-letra-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
