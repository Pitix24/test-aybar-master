<?php

namespace App\Livewire\Cliente\Lote;

use App\Models\Cliente;
use Livewire\Component;
use App\Models\SolicitudDigitalizarLetra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Proyecto;
use App\Models\EstadoSolicitudDigitalizarLetra;
use Illuminate\Support\Facades\Log;
use League\Config\Exception\ValidationException;

class AceptarDigitalizarLetra extends Component
{
    public $lote;
    public $cuota;
    public $unidad_negocio_id = null;
    public $proyecto_id = null;

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required',
            'proyecto_id' => 'required',
        ];
    }

    public function mount($cuota, $lote)
    {
        $this->cuota = $cuota;
        $this->lote = $lote;

        $proyecto = Proyecto::select('id', 'unidad_negocio_id')
            ->where('slin_id', $this->lote['id_proyecto'])
            ->whereHas('unidadNegocio', function ($query) {
                $query->where('slin_id', $this->lote['id_empresa']);
            })
            ->first();

        if ($proyecto) {
            $this->proyecto_id = $proyecto->id;
            $this->unidad_negocio_id = $proyecto->unidad_negocio_id;
        }
    }

    public function guardar()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // 1. Validar que venga el NIT
            if (empty($this->lote['nit'])) {
                throw ValidationException::withMessages([
                    'cliente' => 'No se pudo identificar al cliente (NIT vacío).'
                ]);
            }

            // 2. Buscar cliente por DNI / NIT
            $cliente = Cliente::where('dni', $this->lote['nit'])->first();

            if (!$cliente) {
                throw ValidationException::withMessages([
                    'cliente' => 'El cliente no existe en el sistema.'
                ]);
            }

            // 3. Validar que tenga user asociado
            if (!$cliente->user_id) {
                throw ValidationException::withMessages([
                    'cliente' => 'El cliente no tiene un usuario asociado.'
                ]);
            }

            // 4. Guardar solicitud
            SolicitudDigitalizarLetra::updateOrCreate(
                [
                    'codigo_cuota' => $this->cuota['idCuota'] ?? null,
                ],
                [
                    'unidad_negocio_id' => $this->unidad_negocio_id,
                    'proyecto_id' => $this->proyecto_id,
                    'cliente_id' => $cliente->user_id,
                    'estado_solicitud_digitalizar_letra_id' => EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::PENDIENTE) ?? 1,

                    'razon_social' => $this->lote['razon_social'] ?? null,
                    'nombre_proyecto' => $this->lote['descripcion'] ?? null,
                    'etapa' => $this->lote['id_etapa'] ?? null,
                    'manzana' => $this->lote['id_manzana'] ?? null,
                    'lote' => $this->lote['id_lote'] ?? null,
                    'codigo_cliente' => $this->lote['id_cliente'] ?? null,
                    'numero_cuota' => $this->cuota['NroCuota'] ?? null,
                    'codigo_venta' => $this->lote['id_recaudo'] ?? null,
                    'fecha_vencimiento' => $this->cuota['FecVencimiento'] ?? null,
                    'importe_cuota' => $this->cuota['Cuota'] ?? null,

                    'lote_completo' => ($this->lote['id_proyecto'] ?? '') .
                        ($this->lote['id_etapa'] ?? '') . '-' .
                        ($this->lote['id_manzana'] ?? '') . '-' .
                        ($this->lote['id_lote'] ?? ''),
                ]
            );

            DB::commit();

            session()->flash('success', 'Solicitud enviada correctamente.');
            $this->dispatch('actualizarCronograma');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error al guardar SolicitudDigitalizarLetra', [
                'error' => $e->getMessage(),
                'lote' => $this->lote,
                'cuota' => $this->cuota,
            ]);

            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No se pudo registrar la solicitud.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.cliente.lote.aceptar-digitalizar-letra');
    }
}
