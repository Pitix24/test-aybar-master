<?php

namespace App\Livewire\Erp\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion;
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
    public $tipo_documento    = 'DNI';
    public $numero_documento;
    public $tipo_bien_contratado = 'PRODUCTO';
    public $monto_reclamado;
    public $descripcion;
    public $tipo_pedido       = 'RECLAMO';
    public $detalle;
    public $pedido;
    public $conformidad       = false;

    // Campos de información de la unidad (solo lectura en el formulario)
    public $unidad_nombre;
    public $unidad_razon_social;
    public $unidad_ruc;
    public $unidad_direccion;

    // Catálogos
    public $unidades_negocio = [];
    public $lista_proyectos  = [];

    public $success          = false;
    public $reclamo_registrado;

    protected function rules()
    {
        return [
            'unidad_negocio_id'    => 'nullable|exists:unidad_negocios,id',
            'proyecto_id'          => 'required|exists:proyectos,id',
            'nombre'               => 'required|string|max:255',
            'apellido_paterno'     => 'required|string|max:255',
            'apellido_materno'     => 'required|string|max:255',
            'domicilio'            => 'nullable|string|max:255',
            'telefono'             => 'nullable|string|max:20',
            'email'                => 'required|email|max:255',
            'tipo_documento'       => 'nullable|in:DNI,RUC,CE',
            'numero_documento'     => 'nullable|string|max:20',
            'tipo_bien_contratado' => 'nullable|in:PRODUCTO,SERVICIO',
            'monto_reclamado'      => 'nullable|numeric|min:0',
            'descripcion'          => 'nullable|string',
            'tipo_pedido'          => 'nullable|in:RECLAMO,QUEJA',
            'detalle'              => 'nullable|string',
            'pedido'               => 'nullable|string',
            'conformidad'          => 'accepted',
        ];
    }

    public function validationAttributes()
    {
        return [
            'unidad_negocio_id'    => 'unidad de negocio',
            'proyecto_id'          => 'proyecto',
            'nombre'               => 'nombres',
            'apellido_paterno'     => 'apellido paterno',
            'apellido_materno'     => 'apellido materno',
            'domicilio'            => 'domicilio',
            'tipo_documento'       => 'tipo de documento',
            'numero_documento'     => 'número de documento',
            'tipo_bien_contratado' => 'tipo de bien',
            'descripcion'          => 'descripción del bien',
            'tipo_pedido'          => 'tipo de solicitud',
            'detalle'              => 'detalle del reclamo/queja',
            'pedido'               => 'pedido del consumidor',
            'conformidad'          => 'conformidad con los términos',
        ];
    }

    public function mount()
    {
        $this->lista_proyectos = Proyecto::where('activo', true)
            ->orderBy('nombre')
            ->get();
    }

    public function updatedProyectoId($id)
    {
        $this->unidad_negocio_id    = null;
        $this->unidad_nombre        = '';
        $this->unidad_razon_social  = '';
        $this->unidad_ruc           = '';
        $this->unidad_direccion     = '';

        if ($id) {
            $proyecto = Proyecto::with('unidadNegocio')->find($id);
            if ($proyecto && $proyecto->unidadNegocio) {
                $this->unidad_negocio_id   = $proyecto->unidadNegocio->id;
                $this->unidad_nombre       = $proyecto->unidadNegocio->nombre;
                $this->unidad_razon_social = $proyecto->unidadNegocio->razon_social;
                $this->unidad_ruc          = $proyecto->unidadNegocio->ruc;
                $this->unidad_direccion    = $proyecto->unidadNegocio->direccion;
            }
        }
    }

    public function enviar()
    {
        // ── Validación ──────────────────────────────────────────────────────
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Casi listo',
                'text'  => 'Por favor verifique los campos obligatorios del formulario.',
            ]);
            throw $e;
        }

        // ── Registro en BD ───────────────────────────────────────────────────
        try {
            $reclamo = DB::transaction(function () {
                // Generar ticket y guardar reclamo dentro de la misma transacción.
                $ticket = LibroReclamacion::generarTicket((int) $this->unidad_negocio_id);

                return LibroReclamacion::create([
                    'unidad_negocio_id'    => $this->unidad_negocio_id,
                    'proyecto_id'          => $this->proyecto_id,
                    'nombre'               => $this->nombre,
                    'apellido_paterno'     => $this->apellido_paterno,
                    'apellido_materno'     => $this->apellido_materno,
                    // La columna no admite NULL; se usa cadena vacía si el campo llega vacío.
                    'domicilio'            => $this->domicilio ?? '',
                    'telefono'             => $this->telefono,
                    'email'                => $this->email,
                    'tipo_documento'       => $this->tipo_documento ?: 'DNI',       // MAYÚSCULAS (ENUM)
                    // La columna no admite NULL, por eso persistimos cadena vacía cuando el campo no es obligatorio.
                    'numero_documento'     => $this->numero_documento ?? '',
                    'tipo_bien_contratado' => $this->tipo_bien_contratado ?: 'PRODUCTO', // MAYÚSCULAS (ENUM)
                    'monto_reclamado'      => $this->monto_reclamado,
                    'descripcion'          => $this->descripcion,
                    'tipo_pedido'          => $this->tipo_pedido ?: 'RECLAMO',          // MAYÚSCULAS (ENUM)
                    'detalle'              => $this->detalle,
                    'pedido'               => $this->pedido,
                    'conformidad'          => $this->conformidad,
                    'estado'               => 'NUEVO',
                    // ── Ticket ──────────────────────────────────────────────
                    'serie'                => $ticket['serie'],
                    'numero_reclamo'       => $ticket['numero_reclamo'],
                    'codigo_ticket'        => $ticket['codigo_ticket'],
                ]);
            });

            $this->reclamo_registrado = $reclamo;
            $this->success            = true;

            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => 'Enviado',
                'text'  => 'Su reclamo ha sido registrado con éxito.',
            ]);

            session()->flash('success', 'Tu reclamo ha sido enviado con éxito.');

        } catch (\Exception $e) {
            Log::error('[RECLAMACION] Error al registrar: ' . $e->getMessage(), [
                'data'  => $this->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => 'Error',
                'text'  => 'No se pudo registrar su reclamo. Por favor, intente nuevamente más tarde.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.web.libro-reclamacion.libro-reclamacion-livewire');
    }
}
