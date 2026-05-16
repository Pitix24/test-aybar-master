<?php

namespace App\Livewire\Erp\Marketing\Reglamento;

use App\Models\Reglamento;
use App\Models\Proyecto;
use App\Models\MarketingArchivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Reglamento')]
class ReglamentoCrear extends Component
{
    use WithFileUploads;

    public $proyecto_id;
    public $titulo;
    public $descripcion;
    public $orden = 0;
    public $activo = true;
    public $archivo;

    protected function rules()
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'orden' => 'required|integer',
            'activo' => 'boolean',
            'archivo' => 'required|file|mimes:pdf|max:51200',
        ];
    }

    protected $validationAttributes = [
        'proyecto_id' => 'proyecto',
        'titulo' => 'título',
        'descripcion' => 'descripción',
        'orden' => 'orden',
        'archivo' => 'archivo PDF',
    ];

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function store()
    {
        $this->authorize('reglamento.crear');

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

        try {
            DB::beginTransaction();

            $reglamento = Reglamento::create([
                'proyecto_id' => $this->proyecto_id,
                'titulo' => $this->titulo,
                'descripcion' => $this->descripcion,
                'orden' => $this->orden,
                'activo' => $this->activo,
            ]);

            if ($this->archivo) {
                $path = $this->archivo->store('marketing/reglamentos', 'public');
                $url = Storage::url($path);

                MarketingArchivo::create([
                    'archivable_id' => $reglamento->id,
                    'archivable_type' => Reglamento::class,
                    'user_id' => auth()->id(),
                    'nombre_original' => $this->archivo->getClientOriginalName(),
                    'path' => $path,
                    'url' => $url,
                    'extension' => $this->archivo->getClientOriginalExtension(),
                    'size' => $this->archivo->getSize(),
                    'mime_type' => $this->archivo->getMimeType(),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'Reglamento creado correctamente.'
            ]);

            return redirect()->route('erp.reglamento.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('reglamento')->error("[REGLAMENTO] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el reglamento.'
            ]);
        }
    }

    public function render()
    {
        $proyectos = Proyecto::where('activo', true)->get();

        return view('livewire.erp.marketing.reglamento.reglamento-crear', [
            'proyectos' => $proyectos,
        ]);
    }
}
