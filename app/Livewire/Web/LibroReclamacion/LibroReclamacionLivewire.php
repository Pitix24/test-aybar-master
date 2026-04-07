<?php

namespace App\Livewire\Web\LibroReclamacion;

use App\Models\LibroReclamacion;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.web.layout-web')]
class LibroReclamacionLivewire extends Component
{
    use WithFileUploads;

    // Campos del formulario
    public $unidad_negocio_id;
    public $proyecto_id;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $domicilio;
    public $telefono;
    public $email;
    public $tipo_documento = 'dni';
    public $numero_documento;
    public $tipo_bien_contratado = 'producto';
    public $monto_reclamado;
    public $descripcion;
    public $tipo_pedido = 'reclamo';
    public $detalle;
    public $pedido;
    public $conformidad = false;

    // Campos de información de la unidad (solo lectura en el formulario)
    public $unidad_razon_social;
    public $unidad_ruc;
    public $unidad_direccion;

    // Catálogos
    public $unidades_negocio = [];
    public $lista_proyectos = [];

    public $success = false;
    public $reclamo_registrado;

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'nullable|exists:unidad_negocios,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'domicilio' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'tipo_documento' => 'required|in:dni,ruc,ce',
            'numero_documento' => 'required|string|max:20',
            'tipo_bien_contratado' => 'nullable|in:producto,servicio',
            'monto_reclamado' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'tipo_pedido' => 'nullable|in:reclamo,queja',
            'detalle' => 'nullable|string',
            'pedido' => 'nullable|string',
            'conformidad' => 'accepted',
        ];
    }

    public function validationAttributes()
    {
        return [
            'unidad_negocio_id' => 'unidad de negocio',
            'proyecto_id' => 'proyecto',
            'nombre' => 'nombres',
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'domicilio' => 'domicilio',
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'tipo_bien_contratado' => 'tipo de bien',
            'descripcion' => 'descripción del bien',
            'tipo_pedido' => 'tipo de solicitud',
            'detalle' => 'detalle del reclamo/queja',
            'pedido' => 'pedido del consumidor',
            'conformidad' => 'conformidad con los términos',
        ];
    }

    public function mount()
    {
        $this->unidades_negocio = UnidadNegocio::where('activo', true)->get();
    }

    public function updatedUnidadNegocioId($id)
    {
        $this->proyecto_id = null;
        $this->lista_proyectos = [];
        $this->unidad_razon_social = '';
        $this->unidad_ruc = '';
        $this->unidad_direccion = '';

        if ($id) {
            $unidad = UnidadNegocio::find($id);
            if ($unidad) {
                $this->unidad_razon_social = $unidad->razon_social;
                $this->unidad_ruc = $unidad->ruc;
                $this->unidad_direccion = $unidad->direccion;
                $this->lista_proyectos = Proyecto::where('unidad_negocio_id', $id)->where('activo', true)->get();
            }
        }
    }

    public function enviar()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Casi listo',
                'text' => 'Por favor verifique los campos obligatorios del formulario.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $reclamo = LibroReclamacion::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'nombre' => $this->nombre,
                'apellido_paterno' => $this->apellido_paterno,
                'apellido_materno' => $this->apellido_materno,
                'domicilio' => $this->domicilio,
                'telefono' => $this->telefono,
                'email' => $this->email,
                'tipo_documento' => $this->tipo_documento,
                'numero_documento' => $this->numero_documento,
                'tipo_bien_contratado' => $this->tipo_bien_contratado,
                'monto_reclamado' => $this->monto_reclamado,
                'descripcion' => $this->descripcion,
                'tipo_pedido' => $this->tipo_pedido,
                'detalle' => $this->detalle,
                'pedido' => $this->pedido,
                'conformidad' => $this->conformidad,
                'estado' => 'nuevo',
                'serie' => 'TCK',
            ]);

            DB::commit();

            $this->reclamo_registrado = $reclamo;
            $this->success = true;

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Enviado',
                'text' => 'Su reclamo ha sido registrado con éxito.'
            ]);

            session()->flash('success', 'Tu reclamo ha sido enviado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('reclamacion')->error('[RECLAMACION] Error al registrar: ' . $e->getMessage(), [
                'data' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar su reclamo. Por favor, intente nuevamente más tarde.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.web.libro-reclamacion-livewire');
    }
}
