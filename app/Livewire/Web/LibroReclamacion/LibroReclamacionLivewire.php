<?php

namespace App\Livewire\Web\LibroReclamacion;

use App\Events\LibroReclamacion\LibroReclamacionRegistrado;
use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.web.layout-web')]
class LibroReclamacionLivewire extends Component
{
    use WithFileUploads;

    public $unidad_negocio_id;
    public $proyecto_id;
    public $manzana;
    public $lote;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $domicilio;
    public $telefono;
    public $email;
    public $tipo_documento = null;
    public $numero_documento;
    public $tipo_bien_contratado = null;
    public $monto_reclamado;
    public $descripcion;
    public $tipo_pedido = null;
    public $detalle;
    public $pedido;
    public $conformidad = false;
    public $unidad_razon_social;
    public $lista_proyectos = [];
    public $success = false;
    public $reclamo_registrado;
    protected function rules()
    {
        return [
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'manzana' => 'nullable|string|max:5',
            'lote' => ['nullable', 'regex:/^[0-9]{1,5}$/'],
            'nombre' => 'nullable|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'domicilio' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tipo_documento' => 'nullable|in:dni,ruc,ce,no_definido',
            'numero_documento' => 'nullable|string|max:20',
            'tipo_bien_contratado' => 'nullable|in:producto,servicio,no_definido',
            'monto_reclamado' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'tipo_pedido' => 'nullable|in:reclamo,queja,no_definido',
            'detalle' => 'nullable|string',
            'pedido' => 'nullable|string',
            'conformidad' => 'nullable|boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'proyecto_id' => 'proyecto',
            'manzana' => 'manzana',
            'lote' => 'lote',
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
        $this->lista_proyectos = Proyecto::with('unidadNegocio')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    }

    public function updatedProyectoId($id)
    {
        $this->unidad_negocio_id = null;
        $this->unidad_razon_social = '';

        if ($id) {
            $proyecto = Proyecto::with('unidadNegocio')->find($id);

            if ($proyecto && $proyecto->unidadNegocio) {
                $this->unidad_negocio_id = $proyecto->unidadNegocio->id;
                $this->unidad_razon_social = $proyecto->unidadNegocio->razon_social;
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
                'title' => 'Revise los datos',
                'text' => 'Se detectaron formatos invalidos. Corrija los campos resaltados para continuar.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $proyecto = null;
            $unidadNegocio = null;

            if (! empty($this->proyecto_id)) {
                $proyecto = Proyecto::with('unidadNegocio')->find($this->proyecto_id);
                $unidadNegocio = $proyecto?->unidadNegocio;
            }

            if (! $unidadNegocio) {
                if ($this->esFormularioVacio()) {
                    $unidadNegocio = $this->resolverUnidadNegocioTemplate();
                }
            }

            if (! $unidadNegocio) {
                $unidadNegocio = $this->resolverUnidadNegocioPorDefecto();
            }

            if (! $unidadNegocio) {
                throw new \RuntimeException('No se encontro unidad de negocio para generar el ticket. Configure LIBRO_RECLAMACION_UNIDAD_DEFAULT_ID o seleccione un proyecto valido.');
            }

            $this->unidad_negocio_id = $unidadNegocio->id;
            $this->unidad_razon_social = $unidadNegocio->razon_social ?? $unidadNegocio->nombre;
            $ticket = LibroReclamacion::generarTicket($this->unidad_negocio_id);

            $reclamo = LibroReclamacion::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $proyecto?->id,
                'manzana' => $this->textoNullable($this->manzana),
                'lote' => $this->textoNullable($this->lote),
                'serie' => $ticket['serie'],
                'numero_reclamo' => $ticket['numero_reclamo'],
                'codigo_ticket' => $ticket['codigo_ticket'],
                'nombre' => $this->textoNoNulo($this->nombre),
                'apellido_paterno' => $this->textoNoNulo($this->apellido_paterno),
                'apellido_materno' => $this->textoNoNulo($this->apellido_materno),
                'domicilio' => $this->textoNoNulo($this->domicilio),
                'telefono' => $this->textoNullable($this->telefono),
                'email' => $this->textoNullable($this->email),
                'tipo_documento' => $this->resolverTipoDocumento(),
                'numero_documento' => $this->textoNoNulo($this->numero_documento),
                'tipo_bien_contratado' => $this->resolverTipoBienContratado(),
                'monto_reclamado' => $this->monto_reclamado,
                'descripcion' => $this->textoNullable($this->descripcion),
                'tipo_pedido' => $this->resolverTipoPedido(),
                'detalle' => $this->textoNullable($this->detalle),
                'pedido' => $this->textoNullable($this->pedido),
                'conformidad' => $this->conformidad,
                'estado' => 'NUEVO',
            ]);

            DB::commit();

            // El correo se dispara fuera de la transaccion para no afectar el alta del reclamo.
            LibroReclamacionRegistrado::dispatch($reclamo);

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
            Log::error('[RECLAMACION] Error al registrar: ' . $e->getMessage(), [
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

    protected function resolverUnidadNegocioPorDefecto(): ?UnidadNegocio
    {
        $unidadDefaultId = (int) config('libro_reclamacion.unidad_default_id', 0);

        if ($unidadDefaultId > 0) {
            $unidad = UnidadNegocio::query()->find($unidadDefaultId);

            if ($unidad) {
                return $unidad;
            }

            Log::warning('[RECLAMACION] No existe la unidad por defecto configurada.', [
                'unidad_default_id' => $unidadDefaultId,
            ]);
        }

        // Fallback operativo para no bloquear formulario vacio.
        $unidadActiva = UnidadNegocio::query()
            ->where('activo', true)
            ->orderBy('id')
            ->first();

        if ($unidadActiva instanceof UnidadNegocio) {
            return $unidadActiva;
        }

        $unidad = UnidadNegocio::query()->orderBy('id')->first();

        return $unidad instanceof UnidadNegocio ? $unidad : null;
    }

    protected function resolverUnidadNegocioTemplate(): ?UnidadNegocio
    {
        $templateId = (int) config('libro_reclamacion.unidad_template_id', 0);

        if ($templateId > 0) {
            $unidad = UnidadNegocio::query()->find($templateId);

            if ($unidad instanceof UnidadNegocio) {
                return $unidad;
            }

            Log::warning('[RECLAMACION] No existe la unidad template configurada.', [
                'unidad_template_id' => $templateId,
            ]);
        }

        $nombreTemplate = trim((string) config('libro_reclamacion.unidad_template_nombre', 'RECLAMOS_SIN_PROYECTO'));
        $razonSocialTemplate = trim((string) config('libro_reclamacion.unidad_template_razon_social', 'RECLAMOS SIN PROYECTO'));

        if ($nombreTemplate === '') {
            return null;
        }

        $unidadExistente = UnidadNegocio::withTrashed()
            ->where('nombre', $nombreTemplate)
            ->first();

        if ($unidadExistente instanceof UnidadNegocio) {
            if (method_exists($unidadExistente, 'trashed') && $unidadExistente->trashed()) {
                $unidadExistente->restore();
            }

            if ($unidadExistente->activo !== true) {
                $unidadExistente->activo = true;
                $unidadExistente->save();
            }

            return $unidadExistente;
        }

        return UnidadNegocio::query()->create([
            'nombre' => $nombreTemplate,
            'razon_social' => $razonSocialTemplate !== '' ? $razonSocialTemplate : $nombreTemplate,
            'activo' => true,
        ]);
    }

    protected function esFormularioVacio(): bool
    {
        return empty($this->proyecto_id)
            && $this->textoNullable($this->manzana) === null
            && $this->textoNullable($this->lote) === null
            && $this->textoNullable($this->nombre) === null
            && $this->textoNullable($this->apellido_paterno) === null
            && $this->textoNullable($this->apellido_materno) === null
            && $this->textoNullable($this->domicilio) === null
            && $this->textoNullable($this->telefono) === null
            && $this->textoNullable($this->email) === null
            && $this->esValorNoDefinido($this->tipo_documento)
            && $this->textoNullable($this->numero_documento) === null
            && $this->esValorNoDefinido($this->tipo_bien_contratado)
            && $this->monto_reclamado === null
            && $this->textoNullable($this->descripcion) === null
            && $this->esValorNoDefinido($this->tipo_pedido)
            && $this->textoNullable($this->detalle) === null
            && $this->textoNullable($this->pedido) === null;
    }

    protected function resolverTipoDocumento(): string
    {
        if ($this->esValorNoDefinido($this->tipo_documento)) {
            return 'NO_DEFINIDO';
        }

        return mb_strtoupper((string) $this->tipo_documento);
    }

    protected function resolverTipoBienContratado(): string
    {
        if ($this->esValorNoDefinido($this->tipo_bien_contratado)) {
            return 'NO_DEFINIDO';
        }

        return mb_strtoupper((string) $this->tipo_bien_contratado);
    }

    protected function resolverTipoPedido(): string
    {
        if ($this->esValorNoDefinido($this->tipo_pedido)) {
            return 'NO_DEFINIDO';
        }

        return mb_strtoupper((string) $this->tipo_pedido);
    }

    protected function esValorNoDefinido(mixed $valor): bool
    {
        $texto = trim((string) $valor);

        return $texto === '' || $texto === 'no_definido';
    }

    protected function textoNoNulo(?string $valor): string
    {
        return trim((string) $valor);
    }

    protected function textoNullable(?string $valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    public function render()
    {
        return view('livewire.web.libro-reclamacion.libro-reclamacion-livewire');
    }
}