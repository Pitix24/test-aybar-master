<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Registrar Prospecto - Entrega Fest')]
class EntregaFestProspectoCrear extends Component
{
    public EntregaFest $evento;

    // Campos del prospecto (Sincronizados con migración)
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '', $observacion = '';
    public $lote = '', $manzana = '';
    public $estado = 'pendiente';

    // BackOffice
    public $grupo = 'A', $gestor_backoffice_id = '', $fecha_culminacion_eecc, $link_carpeta_eecc, $link_eecc_firmado;
    public $validador_backoffice_id = '', $fecha_validacion_eecc, $estado_backoffice = 'pendiente';

    // Legal
    public $estado_contrato_preeliminar_emitido = 'pendiente', $estado_firma_contrato_firmado = 'pendiente';
    public $fecha_firma, $fecha_generacion_contrato;

    public $proyectos = [];

    protected function rules()
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'observacion' => 'nullable|string',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',

            // BackOffice
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',
            'validador_backoffice_id' => 'nullable|exists:users,id',
            'fecha_validacion_eecc' => 'nullable|date',
            'estado_backoffice' => 'required|in:pendiente,observado,aprobado,rechazado',

            // Legal
            'estado_contrato_preeliminar_emitido' => 'required|in:pendiente,observado,aprobado,rechazado',
            'estado_firma_contrato_firmado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'fecha_firma' => 'nullable|date',
            'fecha_generacion_contrato' => 'nullable|date',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'proyecto_id' => 'proyecto',
            'dni' => 'DNI',
            'nombres' => 'nombres completos',
            'email' => 'correo electrónico',
            'celular' => 'número de celular',
            'estado' => 'estado del prospecto',
            'grupo' => 'grupo backoffice',
            'estado_backoffice' => 'estado backoffice',
            'estado_contrato_preeliminar_emitido' => 'estado contrato preliminar',
            'estado_firma_contrato_firmado' => 'estado firma contrato',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
    }

    public function store()
    {
        $this->authorize('entrega-fest.prospectos');

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

        // Verificar duplicado en este evento
        $existe = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->where('dni', $this->dni)
            ->exists();

        if ($existe) {
            $this->addError('dni', 'Esta persona ya está registrada para este evento.');
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'DNI Duplicado',
                'text' => 'El prospecto con DNI ' . $this->dni . ' ya existe en este festival.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            ProspectoEntregaFest::create([
                'entrega_fest_id' => $this->evento->id,
                'proyecto_id' => $this->proyecto_id,
                'user_id' => Auth::id(),
                'dni' => $this->dni,
                'nombres' => trim($this->nombres),
                'email' => trim($this->email),
                'celular' => trim($this->celular),
                'lote' => $this->lote,
                'manzana' => $this->manzana,
                'estado' => $this->estado,
                'observacion' => $this->observacion,
                'grupo' => $this->grupo,
                'gestor_backoffice_id' => $this->gestor_backoffice_id ?: null,
                'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
                'link_carpeta_eecc' => $this->link_carpeta_eecc,
                'link_eecc_firmado' => $this->link_eecc_firmado,
                'validador_backoffice_id' => $this->validador_backoffice_id ?: null,
                'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
                'estado_backoffice' => $this->estado_backoffice,
                'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
                'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
                'fecha_firma' => $this->fecha_firma,
                'fecha_generacion_contrato' => $this->fecha_generacion_contrato,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Prospecto ' . $this->nombres . ' registrado correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.prospectos', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[PROSPECTO CREAR] Error al registrar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar el prospecto.'
            ]);
        }
    }

    public function render()
    {
        $usuarios = User::orderBy('name')->get();
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-prospecto-crear', [
            'usuarios' => $usuarios
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
