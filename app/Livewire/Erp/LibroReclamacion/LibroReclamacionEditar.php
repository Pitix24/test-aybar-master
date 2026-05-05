<?php

namespace App\Livewire\Erp\LibroReclamacion;

use App\Models\Cliente;
use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use App\Services\ConsultaClienteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Ticket Libro Reclamacion')]
class LibroReclamacionEditar extends Component
{
    public LibroReclamacion $ticket_model;

    public $codigo = '';
    public $unidad_negocio_id = '';
    public $proyecto_id = '';
    public $cliente_id = '';
    public $cliente_tipo_documento = '';
    public $cliente_documento = '';
    public $cliente_nombre = '';
    public $cliente_email = '';
    public $cliente_celular = '';
    public $cliente_direccion = '';
    public $asunto = '';
    public $gestor_id = '';
    public $clasificacion = 'PENDIENTE_REVISION';
    public $tipo_pedido = '';
    public $observaciones_internas = '';
    public $es_cliente_menor = null;
    public $representante_legal_nombre = '';
    public $representante_legal_apellido_paterno = '';
    public $representante_legal_apellido_materno = '';

    public $unidades = [];
    public $proyectos = [];
    public $usuarios = [];
    public $gestores = [];

    public $dni = '';
    public $lote_id = '';
    public $lotes_agregados = [];

    public Collection $informaciones;

    public function mount($id): void
    {
        $this->authorize('ticket-libro-reclamacion.editar');

        $this->ticket_model = LibroReclamacion::with([
            'ticketRelacionado',
            'unidadNegocio',
            'proyecto',
            'cliente',
            'gestor',
            'creador',
            'actualizador',
            'eliminador',
        ])->findOrFail($id);

        $this->codigo = $this->ticket_model->codigo_ticket;
        $this->unidad_negocio_id = $this->ticket_model->unidad_negocio_id;
        $this->proyecto_id = $this->ticket_model->proyecto_id;
        $this->cliente_id = $this->ticket_model->cliente_id;
        $this->gestor_id = $this->ticket_model->gestor_id;
        $this->clasificacion = $this->ticket_model->clasificacion;
        $this->tipo_pedido = $this->ticket_model->tipo_pedido ?: '';
        $this->observaciones_internas = $this->ticket_model->observaciones_internas;
        $this->cliente_tipo_documento = $this->ticket_model->cliente_tipo_documento;
        $this->cliente_documento = $this->ticket_model->cliente_documento;
        $this->cliente_nombre = $this->ticket_model->cliente_nombre;
        $this->cliente_email = $this->ticket_model->cliente_email;
        $this->cliente_celular = $this->ticket_model->cliente_celular;
        $this->cliente_direccion = $this->ticket_model->cliente_direccion;
        $this->es_cliente_menor = $this->ticket_model->es_cliente_menor ?? false;
        $this->representante_legal_nombre = $this->ticket_model->representante_legal_nombre ?? '';
        $this->representante_legal_apellido_paterno = $this->ticket_model->representante_legal_apellido_paterno ?? '';
        $this->representante_legal_apellido_materno = $this->ticket_model->representante_legal_apellido_materno ?? '';
        $this->asunto = $this->ticket_model->asunto;
        $this->lotes_agregados = $this->ticket_model->lotes ?? [];
        $this->dni = $this->ticket_model->cliente_documento ?? '';

        $this->unidades = UnidadNegocio::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $this->proyectos = Proyecto::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $this->usuarios = User::query()->where('activo', true)->orderBy('name')->get(['id', 'name']);
        $this->gestores = $this->usuarios;
        $this->informaciones = collect();
    }

    protected function rules(): array
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'cliente_documento' => 'nullable|string|max:20',
            'cliente_nombre' => 'required|string|max:255',
            'cliente_email' => 'nullable|email|max:255',
            'cliente_celular' => 'nullable|string|max:30',
            'cliente_direccion' => 'nullable|string',
            'asunto' => 'required|string|max:255',
            'cliente_id' => 'nullable|exists:users,id',
            'gestor_id' => 'nullable|exists:users,id',
            'tipo_pedido' => 'required|in:RECLAMO,QUEJA',
            'observaciones_internas' => 'nullable|string',
            'es_cliente_menor' => 'nullable|boolean',
            'representante_legal_nombre' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
            'representante_legal_apellido_paterno' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
            'representante_legal_apellido_materno' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'unidad_negocio_id' => 'Unidad de negocio',
            'proyecto_id' => 'Proyecto',
            'cliente_documento' => 'DNI / CE / RUC (opcional)',
            'cliente_nombre' => 'Nombre del cliente',
            'cliente_email' => 'Correo electrónico',
            'cliente_celular' => 'Celular',
            'cliente_direccion' => 'Dirección',
            'asunto' => 'Asunto',
            'gestor_id' => 'Gestor',
            'tipo_pedido' => 'Subtipo',
            'observaciones_internas' => 'Observaciones internas',
            'es_cliente_menor' => 'Indicador de menor de edad',
            'representante_legal_nombre' => 'Nombre del representante legal',
            'representante_legal_apellido_paterno' => 'Apellido paterno del representante legal',
        ];
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'unidad_negocio_id') {
            $this->updatedUnidadNegocioId($this->unidad_negocio_id);
        }

        if ($propertyName === 'dni') {
            $this->updatedDni($this->dni);
        }

        if (in_array($propertyName, [
            'unidad_negocio_id',
            'proyecto_id',
            'cliente_documento',
            'cliente_nombre',
            'cliente_email',
            'cliente_celular',
            'cliente_direccion',
            'asunto',
            'gestor_id',
            'tipo_pedido',
            'observaciones_internas',
            'es_cliente_menor',
            'representante_legal_nombre',
            'representante_legal_apellido',
        ], true)) {
            $this->validateOnly($propertyName);
        }
    }

    public function updatedUnidadNegocioId($value): void
    {
        $this->proyecto_id = '';

        if (! $value) {
            $this->proyectos = collect();
            $this->codigo = 'NUL';

            return;
        }

        $this->proyectos = Proyecto::query()
            ->where('unidad_negocio_id', $value)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $ticket = LibroReclamacion::generarTicket((int) $value);
        $this->codigo = (string) ($ticket['codigo_ticket'] ?? 'NUL');
    }

    public function buscarCliente(ConsultaClienteService $service): void
    {
        $this->validate([
            'dni' => 'required|string|max:20',
        ]);

        $this->cliente_documento = trim((string) $this->dni);
        $this->cliente_tipo_documento = $this->resolverTipoDocumento($this->cliente_documento);

        $resultado = $service->consultar($this->dni);

        switch ($resultado['estado']) {
            case 'ok':
                $this->informaciones = collect($resultado['data']);
                $this->hidratarClienteDesdeResultado($resultado);
                session()->flash('success', $resultado['mensaje']);
                break;

            case 'cliente_sin_lotes':
                $this->informaciones = collect();
                $this->hidratarClienteDesdeResultado($resultado);
                session()->flash('info', $resultado['mensaje']);
                break;

            case 'no_cliente':
            case 'error':
            default:
                $this->informaciones = collect();
                session()->flash('error', $resultado['mensaje']);
                break;
        }
    }

    public function updatedDni($value): void
    {
        $this->sincronizarDocumentoDesdeDni((string) $value);
    }

    protected function sincronizarDocumentoDesdeDni(?string $valor = null): void
    {
        $documento = trim((string) ($valor ?? $this->dni));

        $this->cliente_documento = $documento;

        if ($documento === '') {
            $this->cliente_tipo_documento = '';

            return;
        }

        $this->cliente_tipo_documento = $this->resolverTipoDocumento($documento);
    }

    protected function hidratarClienteDesdeResultado(array $resultado): void
    {
        $this->cliente_documento = trim((string) $this->dni);
        $this->cliente_tipo_documento = $this->resolverTipoDocumento($this->cliente_documento);

        if (($resultado['origen'] ?? '') === 'antiguo') {
            $cliente = DB::table('clientes_2')->where('dni', $this->dni)->first();

            $this->cliente_id = null;
            $this->cliente_nombre = (string) data_get($cliente, 'nombre', $this->cliente_nombre);
            $this->cliente_email = (string) data_get($cliente, 'email', $this->cliente_email);
            $this->cliente_celular = (string) (data_get($cliente, 'celular') ?? data_get($cliente, 'telefono') ?? $this->cliente_celular);
            $this->cliente_direccion = (string) (data_get($cliente, 'direccion') ?? data_get($cliente, 'domicilio') ?? $this->cliente_direccion);

            return;
        }

        $cliente = Cliente::query()->where('dni', $this->dni)->with('user')->first();

        if ($cliente && $cliente->user) {
            $this->cliente_id = $cliente->user->id;
            $this->cliente_nombre = (string) $cliente->user->name;
            $this->cliente_email = (string) ($cliente->user->email ?? $cliente->email ?? '');
            $this->cliente_celular = (string) ($cliente->telefono_principal ?? $cliente->telefono_alternativo ?? '');
            return;
        }

        $firstLot = collect($resultado['data'] ?? [])->first();

        $this->cliente_id = null;
        $this->cliente_nombre = (string) data_get($firstLot, 'nombre', $this->cliente_nombre);
        $this->cliente_email = (string) data_get($firstLot, 'email', $this->cliente_email);
        $this->cliente_celular = (string) data_get($firstLot, 'celular', $this->cliente_celular);
    }

    public function agregarLote(): void
    {
        if (! $this->lote_id) {
            return;
        }

        $lote = $this->informaciones->firstWhere('id', $this->lote_id);

        if (! $lote) {
            return;
        }

        if (collect($this->lotes_agregados)->firstWhere('id', $lote->id)) {
            return;
        }

        $this->lotes_agregados[] = [
            'id' => $lote->id,
            'razon_social' => $lote->razon_social,
            'codigo_cliente' => $lote->codigo_cliente,
            'proyecto' => $lote->proyecto,
            'numero_lote' => $lote->numero_lote,
            'estado_lote' => $lote->estado_lote,
        ];

        $this->lote_id = '';
    }

    public function quitarLote(string $id): void
    {
        $this->lotes_agregados = collect($this->lotes_agregados)
            ->reject(fn($l) => (string) ($l['id'] ?? '') === $id)
            ->values()
            ->toArray();
    }

    public function update(): void
    {
        $this->authorize('ticket-libro-reclamacion.editar');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $primerError = collect($e->validator->errors()->all())->first();

            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => $primerError
                    ? 'Validacion: ' . $primerError
                    : 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->sincronizarDocumentoDesdeDni();

            $asignadoAntes = $this->ticket_model->gestor_id;
            $asignadoNuevo = $this->gestor_id ?: null;
            $clasificacion = $this->resolverClasificacion();

            $this->ticket_model->update([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'cliente_id' => $this->cliente_id ?: null,
                'cliente_tipo_documento' => $this->cliente_tipo_documento ?: null,
                'cliente_documento' => $this->cliente_documento,
                'cliente_nombre' => $this->cliente_nombre,
                'cliente_email' => $this->cliente_email ?: null,
                'cliente_celular' => $this->cliente_celular ?: null,
                'cliente_direccion' => $this->cliente_direccion ?: null,
                'es_cliente_menor' => (bool) $this->es_cliente_menor,
                'representante_legal_nombre' => trim($this->representante_legal_nombre) ?: null,
                'representante_legal_apellido_paterno' => trim($this->representante_legal_apellido_paterno) ?: null,
                'representante_legal_apellido_materno' => trim($this->representante_legal_apellido_materno) ?: null,
                'asunto' => $this->asunto,
                'lotes' => $this->lotes_agregados,
                'gestor_id' => $asignadoNuevo,
                'tipo_pedido' => $this->resolverTipoPedido(),
                'clasificacion' => $clasificacion,
                'observaciones_internas' => $this->textoNullable($this->observaciones_internas),
                'assigned_at' => $this->resolverAssignedAt($asignadoAntes, $asignadoNuevo),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'El ticket de libro de reclamacion se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[TICKET-LIBRO] Error al actualizar: ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'target_id' => $this->ticket_model->ticket,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el ticket de libro de reclamacion.'
            ]);
        }
    }

    protected function resolverClasificacion(): string
    {
        if (blank($this->cliente_nombre) || blank($this->asunto)) {
            return 'NO_PROCEDE';
        }

        if (empty($this->lotes_agregados)) {
            return 'PENDIENTE_REVISION';
        }

        return 'PROCEDE';
    }

    protected function resolverTipoPedido(): string
    {
        $tipoPedido = strtoupper(trim((string) $this->tipo_pedido));

        return in_array($tipoPedido, ['RECLAMO', 'QUEJA'], true) ? $tipoPedido : 'RECLAMO';
    }

    protected function resolverTipoDocumento(string $documento): string
    {
        $longitud = strlen(preg_replace('/\D+/', '', $documento));

        return match (true) {
            $longitud >= 11 => 'RUC',
            $longitud === 8 => 'DNI',
            default => 'CE',
        };
    }

    #[On('eliminarLibroTicketOn')]
    public function eliminarLibroTicketOn()
    {
        $this->authorize('ticket-libro-reclamacion.eliminar');

        try {
            DB::beginTransaction();

            $codigo = $this->ticket_model->codigo_ticket;
            $this->ticket_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => "El ticket $codigo fue eliminado correctamente."
            ]);

            return redirect()->route('erp.libro-reclamacion.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[TICKET-LIBRO] Error al eliminar: ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'target_id' => $this->ticket_model->ticket ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el ticket de libro de reclamacion.'
            ]);
        }
    }

    protected function resolverAssignedAt(?int $antes, ?int $nuevo)
    {
        if ($nuevo && $antes !== $nuevo) {
            return now();
        }

        return $this->ticket_model->assigned_at;
    }

    protected function textoNullable(?string $valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    public function render()
    {
        return view('livewire.erp.libro-reclamacion.libro-reclamacion-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
