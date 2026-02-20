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
use Illuminate\Validation\ValidationException;

class AceptarDigitalizarLetra extends Component
{
    public $lote;
    public $cuota;
    public $unidad_negocio_id = null;
    public $proyecto_id = null;

    // Campos adicionales para Admin
    public $dni = '';
    public $nombres = '';
    public $email = '';
    public $celular = '';
    public $direccion = '';

    protected function rules()
    {
        $rules = [
            'unidad_negocio_id' => 'required',
            'proyecto_id' => 'required',
        ];

        if (Auth::user()->rol === 'admin') {
            $rules['dni'] = 'required';
            $rules['nombres'] = 'required';
            $rules['email'] = 'required|email';
            $rules['celular'] = 'required|min:9';
            $rules['direccion'] = 'required|min:10';
        }

        return $rules;
    }

    public function mount($cuota, $lote)
    {
        $this->cuota = $cuota;
        $this->lote = $lote;
        $this->dni = $this->lote['nit'];
        $this->nombres = $this->lote['apellidos_nombres'] ?? '';

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
        try {
            $this->validate();
        } catch (ValidationException $e) {
            session()->flash('error', 'Verifique los errores de los campos resaltados.');
            throw $e;
        }

        try {
            DB::beginTransaction();

            // 1. Validar que venga el NIT (Opcional, pero útil para identificar)
            $nit = $this->lote['nit'] ?? null;

            // 2. Intentar buscar cliente por DNI / NIT
            $cliente = $nit ? Cliente::where('dni', $nit)->first() : null;
            $cliente_id = $cliente ? $cliente->user_id : null;

            // 4. Guardar solicitud (cliente_id ahora puede ser null)
            SolicitudDigitalizarLetra::updateOrCreate(
                [
                    'codigo_cuota' => $this->cuota['idCuota'] ?? null,
                ],
                [
                    'unidad_negocio_id' => $this->unidad_negocio_id,
                    'proyecto_id' => $this->proyecto_id,
                    'cliente_id' => $cliente_id,
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

                    // Campos de Admin
                    'gestor_id' => Auth::user()->rol === 'admin' ? Auth::id() : null,
                    'dni' => $this->dni ?: ($this->lote['nit'] ?? null),
                    'nombres' => $this->nombres ?: null,
                    'email' => $this->email ?: null,
                    'celular' => $this->celular ?: null,
                    'direccion' => $this->direccion ?: null,
                    'origen' => 'slin',
                ]
            );

            DB::commit();

            session()->flash('success', 'Solicitud enviada correctamente.');
            $this->dispatch('actualizarCronograma');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('letra')->error("[LETRA] Error al registrar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'No se pudo registrar la solicitud.');
        }
    }

    public function render()
    {
        return view('livewire.cliente.lote.aceptar-digitalizar-letra');
    }
}
