<?php

namespace App\Livewire\Web\LibroReclamacion;

use App\Events\LibroReclamacion\LibroReclamacionRegistrado;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\Proyecto;
use App\Models\SubTipoSolicitud;
use App\Models\Ticket;
use App\Models\TipoSolicitud;
use App\Models\UnidadNegocio;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.web.layout-web')]
class LibroReclamacionLivewire extends Component
{
    use WithFileUploads;

    public array $payload_ticket_autocreacion = [];

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
    public $es_cliente_menor = null;
    public $representante_legal_nombre = '';
    public $representante_legal_apellido_paterno = '';
    public $representante_legal_apellido_materno = '';
    public $unidad_razon_social;
    public $unidad_ruc;
    public $unidad_direccion;
    public $lista_proyectos = [];
    public $success = false;
    public $reclamo_registrado;
    public $mostrar_advertencia_no_procede = false;
    public $mensaje_resultado = 'Tu reclamo ha sido enviado con éxito.';
    public $estilo_resultado = 'success';
    public $icono_resultado = 'fa-circle-check';

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
            'es_cliente_menor' => 'nullable|boolean',
            'representante_legal_nombre' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
            'representante_legal_apellido_paterno' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
            'representante_legal_apellido_materno' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
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
            'es_cliente_menor' => 'indicador de menor de edad',
            'representante_legal_nombre' => 'nombre del representante legal',
            'representante_legal_apellido_paterno' => 'apellido paterno del representante legal',
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
                $this->unidad_ruc = $proyecto->unidadNegocio->ruc;
                $this->unidad_direccion = $proyecto->unidadNegocio->direccion;
            }
        }
    }

    public function updatedMontoReclamado($value): void
    {
        $this->monto_reclamado = $this->normalizarMontoReclamado($value);
    }

    public function updated($propertyName): void
    {
        if ($propertyName !== 'mostrar_advertencia_no_procede' && $this->mostrar_advertencia_no_procede) {
            $this->mostrar_advertencia_no_procede = false;
        }
    }

    public function registrar(): void
    {
        $clasificacion = $this->resolverClasificacionWeb();

        if ($clasificacion === 'NO_PROCEDE' && !$this->mostrar_advertencia_no_procede) {
            $this->mostrar_advertencia_no_procede = true;

            return;
        }

        $this->enviar();
    }

    public function confirmarEnvioNoProcede(): void
    {
        $this->mostrar_advertencia_no_procede = false;
        $this->enviar();
    }

    public function cancelarAdvertenciaNoProcede(): void
    {
        $this->mostrar_advertencia_no_procede = false;
    }

    public function enviar()
    {
        $this->mostrar_advertencia_no_procede = false;

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

            if (!empty($this->proyecto_id)) {
                $proyecto = Proyecto::with('unidadNegocio')->find($this->proyecto_id);
                $unidadNegocio = $proyecto?->unidadNegocio;
            }

            if (!$unidadNegocio) {
                if ($this->esFormularioVacio()) {
                    $unidadNegocio = $this->resolverUnidadNegocioTemplate();
                }
            }

            if (!$unidadNegocio) {
                $unidadNegocio = $this->resolverUnidadNegocioPorDefecto();
            }

            if (!$unidadNegocio) {
                throw new \RuntimeException('No se encontro unidad de negocio para generar el ticket. Configure LIBRO_RECLAMACION_UNIDAD_DEFAULT_ID o seleccione un proyecto valido.');
            }

            $this->unidad_negocio_id = $unidadNegocio->id;
            $this->unidad_razon_social = $unidadNegocio->razon_social ?? $unidadNegocio->nombre;

            $clasificacion = $this->resolverClasificacionWeb();
            $this->payload_ticket_autocreacion = [];

            $ticketVinculado = null;

            if ($clasificacion !== 'NO_PROCEDE' && config('libro_reclamacion_ticket.ticket_autocreacion.habilitado', true)) {
                $this->payload_ticket_autocreacion = $this->construirPayloadTicketAutocreacion($proyecto?->id);
                $ticketVinculado = $this->crearTicketAutogenerado($this->payload_ticket_autocreacion);
            }

            $ticket = LibroReclamacion::generarTicket($this->unidad_negocio_id);

            $reclamo = LibroReclamacion::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $proyecto?->id,
                'ticket_id' => $ticketVinculado?->id,
                'manzana' => $this->textoNullable($this->manzana),
                'lote' => $this->textoNullable($this->lote),
                'serie' => $ticket['serie'],
                'numero_reclamo' => $ticket['numero_reclamo'],
                'codigo_ticket' => $ticket['codigo_ticket'],
                'cliente_tipo_documento' => $this->resolverTipoDocumento(),
                'cliente_documento' => $this->textoNullable($this->numero_documento),
                'cliente_nombre' => $this->resolverNombreClienteCanonico(),
                'cliente_email' => $this->textoNullable($this->email),
                'cliente_celular' => $this->textoNullable($this->telefono),
                'cliente_direccion' => $this->textoNullable($this->domicilio),
                'es_cliente_menor' => (bool) $this->es_cliente_menor,
                'representante_legal_nombre' => $this->textoNullable($this->representante_legal_nombre),
                'representante_legal_apellido_paterno' => $this->textoNullable($this->representante_legal_apellido_paterno),
                'representante_legal_apellido_materno' => $this->textoNullable($this->representante_legal_apellido_materno),
                'tipo_bien_contratado' => $this->resolverTipoBienContratado(),
                'monto_reclamado' => $this->decimalNullable($this->monto_reclamado),
                'descripcion' => $this->textoNullable($this->descripcion),
                'tipo_pedido' => $this->resolverTipoPedido(),
                'detalle' => $this->textoNullable($this->detalle),
                'pedido' => $this->textoNullable($this->pedido),
                'conformidad' => $this->conformidad,
                'clasificacion' => $clasificacion,
                'estado' => 'NUEVO',
                'created_by' => $this->resolverUsuarioSistemaId(),
            ]);

            DB::commit();

            // El correo se dispara fuera de la transaccion para no afectar el alta del reclamo.
            LibroReclamacionRegistrado::dispatch($reclamo);

            $this->reclamo_registrado = $reclamo;
            $this->success = true;

            if ($clasificacion === 'NO_PROCEDE') {
                $this->mensaje_resultado = 'Tu reclamo se encuentra siendo validado por nuestro equipo. Recordarle que su apoyo con los datos necesarios agiliza este proceso.';
                $this->estilo_resultado = 'info';
                $this->icono_resultado = 'fa-circle-info';

                $this->dispatch('alertaLivewire', [
                    'type' => 'info',
                    'title' => 'En validación',
                    'text' => $this->mensaje_resultado,
                ]);
            } else {
                $this->mensaje_resultado = 'Tu reclamo ha sido enviado con éxito.';
                $this->estilo_resultado = 'success';
                $this->icono_resultado = 'fa-circle-check';

                $this->dispatch('alertaLivewire', [
                    'type' => 'success',
                    'title' => 'Enviado',
                    'text' => 'Su reclamo ha sido registrado con éxito.'
                ]);
            }

            session()->flash('success', $this->mensaje_resultado);
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
        $unidadDefaultId = (int) config('libro_reclamacion_ticket.unidad_default_id', 0);

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
        $templateId = (int) config('libro_reclamacion_ticket.unidad_template_id', 0);

        if ($templateId > 0) {
            $unidad = UnidadNegocio::query()->find($templateId);

            if ($unidad instanceof UnidadNegocio) {
                return $unidad;
            }

            Log::warning('[RECLAMACION] No existe la unidad template configurada.', [
                'unidad_template_id' => $templateId,
            ]);
        }

        $nombreTemplate = trim((string) config('libro_reclamacion_ticket.unidad_template_nombre', 'RECLAMOS_SIN_PROYECTO'));
        $razonSocialTemplate = trim((string) config('libro_reclamacion_ticket.unidad_template_razon_social', 'RECLAMOS SIN PROYECTO'));

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
            && $this->decimalNullable($this->monto_reclamado) === null
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

    protected function resolverClasificacionWeb(): string
    {
        if ($this->esFormularioVacio()) {
            return 'NO_PROCEDE';
        }

        if (!$this->tieneDatosMinimosSeguimiento()) {
            return 'NO_PROCEDE';
        }

        if (!$this->tieneDatosMinimosProcedencia()) {
            return 'PENDIENTE_REVISION';
        }

        return 'PROCEDE';
    }

    protected function tieneDatosMinimosSeguimiento(): bool
    {
        $nombreCompleto = $this->tieneNombreCompleto();
        $tieneDocumento = $this->textoNullable($this->numero_documento) !== null;
        $tieneEmail = $this->textoNullable($this->email) !== null;
        $tieneCelular = $this->textoNullable($this->telefono) !== null;
        $tieneUbicacion = $this->tieneProyectoOUbicacion();

        $contactoDirecto = $nombreCompleto && ($tieneDocumento || $tieneEmail || $tieneCelular);
        $busquedaPorUbicacion = $tieneUbicacion && ($tieneDocumento || $nombreCompleto);

        return $contactoDirecto || $busquedaPorUbicacion;
    }

    protected function tieneDatosMinimosProcedencia(): bool
    {
        $tipoPedidoResuelto = $this->resolverTipoPedido();
        $tieneDetalleCaso = $this->textoNullable($this->detalle) !== null
            || $this->textoNullable($this->pedido) !== null
            || $this->textoNullable($this->descripcion) !== null;

        return $this->tieneDatosMinimosSeguimiento()
            && $this->tieneProyectoOUbicacion()
            && $tipoPedidoResuelto !== 'NO_DEFINIDO'
            && $tieneDetalleCaso;
    }

    protected function tieneNombreCompleto(): bool
    {
        return $this->textoNullable($this->nombre) !== null
            && $this->textoNullable($this->apellido_paterno) !== null;
    }

    protected function tieneProyectoOUbicacion(): bool
    {
        return !empty($this->proyecto_id)
            || ($this->textoNullable($this->manzana) !== null && $this->textoNullable($this->lote) !== null);
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

    protected function decimalNullable(mixed $valor): ?string
    {
        $texto = $this->normalizarMontoReclamado($valor);

        return $texto === '' ? null : $texto;
    }

    protected function normalizarMontoReclamado(mixed $valor): string
    {
        $texto = trim((string) $valor);

        if ($texto === '') {
            return '';
        }

        $texto = str_replace([' ', ','], ['', '.'], $texto);

        return is_numeric($texto) ? (string) $texto : $texto;
    }

    protected function resolverNombreClienteCanonico(): ?string
    {
        $partes = array_filter([
            $this->textoNullable($this->nombre),
            $this->textoNullable($this->apellido_paterno),
            $this->textoNullable($this->apellido_materno),
        ]);

        $nombre = trim(implode(' ', $partes));

        return $nombre === '' ? null : $nombre;
    }

    protected function construirPayloadTicketAutocreacion(?int $proyectoId = null): array
    {
        $tipoPedido = $this->resolverTipoPedido();
        $documento = $this->textoNullable($this->numero_documento);
        $detalle = $this->textoNullable($this->detalle);
        $pedido = $this->textoNullable($this->pedido);

        return [
            'area_id' => (int) config('libro_reclamacion_ticket.ticket_autocreacion.area_legal_id', 3),
            'canal_id' => $this->resolverCanalTicketId(),
            'tipo_solicitud_id' => $this->resolverTipoSolicitudTicketId(),
            'sub_tipo_solicitud_id' => $this->resolverSubTipoSolicitudTicketId($tipoPedido),
            'estado_ticket_id' => $this->resolverEstadoTicketNuevoId(),
            'prioridad_ticket_id' => (int) config('libro_reclamacion_ticket.ticket_autocreacion.prioridad_ticket_id', 3),
            'created_by' => $this->resolverUsuarioSistemaId(),
            'gestor_id' => $this->resolverGestorTicketId(),
            'unidad_negocio_id' => $this->unidad_negocio_id,
            'proyecto_id' => $proyectoId,
            'dni' => $documento,
            'nombres' => trim(implode(' ', array_filter([
                $this->textoNullable($this->nombre),
                $this->textoNullable($this->apellido_paterno),
                $this->textoNullable($this->apellido_materno),
            ]))),
            'email' => $this->textoNullable($this->email),
            'celular' => $this->textoNullable($this->telefono),
            'direccion' => $this->textoNullable($this->domicilio),
            'asunto_inicial' => $this->construirAsuntoInicialTicket($tipoPedido, $documento),
            'descripcion_inicial' => $this->construirDescripcionInicialTicket($detalle, $pedido),
            'origen' => 'FORMULARIO_WEB_LIBRO_RECLAMACION',
            'lotes' => null,
        ];
    }

    protected function crearTicketAutogenerado(array $payload): Ticket
    {
        return Ticket::query()->create([
            'unidad_negocio_id' => data_get($payload, 'unidad_negocio_id') ?: null,
            'proyecto_id' => data_get($payload, 'proyecto_id') ?: null,
            'cliente_id' => null,
            'gestor_id' => data_get($payload, 'gestor_id') ?: $this->resolverGestorTicketId(),
            'area_id' => data_get($payload, 'area_id') ?: null,
            'ticket_padre_id' => null,
            'tipo_solicitud_id' => data_get($payload, 'tipo_solicitud_id') ?: null,
            'sub_tipo_solicitud_id' => data_get($payload, 'sub_tipo_solicitud_id') ?: null,
            'canal_id' => data_get($payload, 'canal_id') ?: null,
            'estado_ticket_id' => data_get($payload, 'estado_ticket_id') ?: 1,
            'prioridad_ticket_id' => data_get($payload, 'prioridad_ticket_id') ?: 3,
            'asunto_inicial' => data_get($payload, 'asunto_inicial') ?: 'LIBRO DE RECLAMACIONES',
            'descripcion_inicial' => data_get($payload, 'descripcion_inicial') ?: 'Sin detalle proporcionado por el cliente.',
            'lotes' => data_get($payload, 'lotes'),
            'dni' => data_get($payload, 'dni') ?: null,
            'nombres' => data_get($payload, 'nombres') ?: null,
            'email' => data_get($payload, 'email') ?: null,
            'celular' => data_get($payload, 'celular') ?: null,
            'direccion' => data_get($payload, 'direccion') ?: null,
            'origen' => data_get($payload, 'origen') ?: 'FORMULARIO_WEB_LIBRO_RECLAMACION',
            'created_by' => data_get($payload, 'created_by') ?: $this->resolverUsuarioSistemaId(),
        ]);
    }

    protected function construirAsuntoInicialTicket(string $tipoPedido, ?string $documento): string
    {
        $formato = (string) config('libro_reclamacion_ticket.ticket_autocreacion.asunto.formato', ':tipo_pedido - :documento');
        $tipo = $tipoPedido !== 'NO_DEFINIDO'
            ? $tipoPedido
            : (string) config('libro_reclamacion_ticket.ticket_autocreacion.asunto.tipo_default', 'NO_DEFINIDO');
        $dni = $documento ?: (string) config('libro_reclamacion_ticket.ticket_autocreacion.asunto.documento_default', 'SIN DOCUMENTO');

        return strtr($formato, [
            ':tipo_pedido' => $tipo,
            ':documento' => $dni,
        ]);
    }

    protected function construirDescripcionInicialTicket(?string $detalle, ?string $pedido): string
    {
        $prefijoDetalle = (string) config('libro_reclamacion_ticket.ticket_autocreacion.descripcion.prefijo_detalle', 'Cliente detalla lo siguiente:');
        $prefijoPedido = (string) config('libro_reclamacion_ticket.ticket_autocreacion.descripcion.prefijo_pedido', 'Cliente pide lo siguiente:');

        $partes = [];

        if ($detalle) {
            $partes[] = $prefijoDetalle . ' ' . $detalle;
        }

        if ($pedido) {
            $partes[] = $prefijoPedido . ' ' . $pedido;
        }

        if (empty($partes)) {
            return '';
        }

        return implode("\n\n", $partes);
    }

    protected function resolverCanalTicketId(): ?int
    {
        $canalId = config('libro_reclamacion_ticket.ticket_autocreacion.canal_id');

        if ($canalId !== null && $canalId !== '') {
            return (int) $canalId;
        }

        $canalNombre = trim((string) config('libro_reclamacion_ticket.ticket_autocreacion.canal_nombre', 'FORMULARIO WEB'));

        if ($canalNombre === '') {
            return null;
        }

        return Canal::query()
            ->whereRaw('UPPER(nombre) = ?', [mb_strtoupper($canalNombre)])
            ->value('id');
    }

    protected function resolverTipoSolicitudTicketId(): ?int
    {
        $tipoSolicitudId = (int) config('libro_reclamacion_ticket.ticket_autocreacion.tipo_solicitud_id', 0);

        if ($tipoSolicitudId > 0) {
            $existe = TipoSolicitud::query()->whereKey($tipoSolicitudId)->exists();

            if ($existe) {
                return $tipoSolicitudId;
            }
        }

        $tipoSolicitudNombre = trim((string) config('libro_reclamacion_ticket.ticket_autocreacion.tipo_solicitud_nombre', 'LIBRO DE RECLAMACIONES'));

        if ($tipoSolicitudNombre === '') {
            return null;
        }

        return TipoSolicitud::query()
            ->whereRaw('UPPER(nombre) = ?', [mb_strtoupper($tipoSolicitudNombre)])
            ->value('id');
    }

    protected function resolverSubTipoSolicitudTicketId(string $tipoPedido): ?int
    {
        $tipoSolicitudId = $this->resolverTipoSolicitudTicketId();

        if (!$tipoSolicitudId) {
            return null;
        }

        $subTipoNombre = trim((string) config('libro_reclamacion_ticket.ticket_autocreacion.subtipo_por_tipo_pedido.' . $tipoPedido, ''));

        if ($subTipoNombre === '') {
            return null;
        }

        return SubTipoSolicitud::query()
            ->where('tipo_solicitud_id', $tipoSolicitudId)
            ->whereRaw('UPPER(nombre) = ?', [mb_strtoupper($subTipoNombre)])
            ->value('id');
    }

    protected function resolverEstadoTicketNuevoId(): ?int
    {
        return EstadoTicket::query()
            ->where('nombre', EstadoTicket::NUEVO)
            ->value('id');
    }

    protected function resolverUsuarioSistemaId(): ?int
    {
        $usuarioId = (int) config('libro_reclamacion_ticket.ticket_autocreacion.created_by', 3066);

        if ($usuarioId <= 0) {
            return null;
        }

        return User::query()->whereKey($usuarioId)->exists() ? $usuarioId : null;
    }

    protected function resolverGestorTicketId(): ?int
    {
        $gestorId = config('libro_reclamacion_ticket.ticket_autocreacion.gestor_id');

        if ($gestorId !== null && $gestorId !== '') {
            $gestorId = (int) $gestorId;

            if ($gestorId > 0 && User::query()->whereKey($gestorId)->exists()) {
                return $gestorId;
            }
        }

        return $this->resolverUsuarioSistemaId();
    }

    public function render()
    {
        return view('livewire.web.libro-reclamacion.libro-reclamacion-livewire');
    }
}
