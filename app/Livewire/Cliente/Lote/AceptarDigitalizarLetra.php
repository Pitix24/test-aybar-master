<?php

namespace App\Livewire\Cliente\Lote;

use App\Models\Cliente;
use App\Models\Distrito;
use App\Models\Provincia;
use App\Models\Region;
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
    public $pais_id = '';
    public $region_id = '';
    public $provincia_id = '';
    public $distrito_id = '';

    public $paises = [];
    public $regions = [];
    public $provincias = [];
    public $distritos = [];

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
            $rules['pais_id'] = 'required';
            $rules['region_id'] = 'required_if:pais_id,1';
            $rules['provincia_id'] = 'required_if:pais_id,1';
            $rules['distrito_id'] = 'required_if:pais_id,1';
        }

        return $rules;
    }

    public function mount($cuota, $lote)
    {
        $this->cuota = $cuota;
        $this->lote = $lote;
        $this->dni = $this->lote['nit'] ?? '';
        $this->nombres = $this->lote['apellidos_nombres'] ?? '';

        // Buscar información del cliente si existe en la DB para pre-llenar datos
        if ($this->dni) {
            $cliente = Cliente::with(['user.direccion'])->where('dni', $this->dni)->first();
            if ($cliente) {
                $this->email = $cliente->email;
                $this->celular = $cliente->telefono_principal;

                if ($cliente->user && $cliente->user->direccion) {
                    $dir = $cliente->user->direccion;
                    $this->direccion = $dir->direccion;
                    $this->pais_id = $dir->pais_id;
                    $this->region_id = $dir->region_id;
                    $this->provincia_id = $dir->provincia_id;
                    $this->distrito_id = $dir->distrito_id;
                }
            }
        }

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

        if (Auth::user()->rol === 'admin') {
            $this->paises = \App\Models\Pais::orderBy('id')->get();
            $this->regions = Region::all();

            // Cargar listas dependientes si hay datos pre-cargados
            if ($this->region_id) {
                $this->provincias = Provincia::where('region_id', $this->region_id)->get();
            }
            if ($this->provincia_id) {
                $this->distritos = Distrito::where('provincia_id', $this->provincia_id)->get();
            }
        }
    }

    public function updatedPaisId($value)
    {
        $this->region_id = '';
        $this->provincia_id = '';
        $this->distrito_id = '';
        $this->provincias = [];
        $this->distritos = [];
    }

    public function updatedRegionId($value)
    {
        $this->provincia_id = '';
        $this->distrito_id = '';
        $this->distritos = [];
        $this->provincias = $value ? Provincia::where('region_id', $value)->get() : [];
    }

    public function updatedProvinciaId($value)
    {
        $this->distrito_id = '';
        $this->distritos = $value ? Distrito::where('provincia_id', $value)->get() : [];
    }

    public function guardar()
    {
        if (session()->has('impersonator_id')) {
            session()->flash('error', 'Como administrador, usted solo tiene permisos de visualización. No puede realizar cambios en la cuenta del cliente.');
            return;
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            session()->flash('error', 'Verifique los errores de los campos resaltados.');
            throw $e;
        }

        try {
            DB::beginTransaction();

            $nit = $this->lote['nit'] ?? null;
            $cliente = $nit ? Cliente::where('dni', $nit)->first() : null;
            $cliente_id = $cliente ? $cliente->user_id : null;

            $pais_nombre = null;
            $region_nombre = null;
            $provincia_nombre = null;
            $distrito_nombre = null;

            if ($this->pais_id) {
                $pais_nombre = \App\Models\Pais::find($this->pais_id)?->nombre ?? null;
            }
            if ($this->region_id) {
                $region_nombre = Region::find($this->region_id)?->nombre ?? null;
            }
            if ($this->provincia_id) {
                $provincia_nombre = Provincia::find($this->provincia_id)?->nombre ?? null;
            }
            if ($this->distrito_id) {
                $distrito_nombre = Distrito::find($this->distrito_id)?->nombre ?? null;
            }

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
                    'pais' => $pais_nombre,
                    'region' => $region_nombre,
                    'provincia' => $provincia_nombre,
                    'distrito' => $distrito_nombre,
                    'origen' => Auth::user()->rol === 'admin' ? 'slin' : 'portal',
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
