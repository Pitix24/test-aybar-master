<div>
    <x-pagina-cabecera titulo="Importar Consolidado Histórico" />

    <div class="panel">
        <div class="panel-body">
            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Acerca de la importación del histórico</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Sube el archivo Excel con la base consolidada de todos los clientes. El sistema realizará un <strong>Upsert</strong>: creará los registros nuevos y actualizará los existentes usando la combinación de (Proyecto + DNI + Lote + Manzana) como clave única.</p>
                            <p class="mt-1"><strong>Campo Clave:</strong> La columna <code class="font-bold">lote_entregado</code> debe contener "SI" (o 1) para aquellos clientes que ya firmaron y no deben ser invitados en el futuro.</p>
                        </div>
                        <div class="mt-4">
                            <button wire:click="descargarPlantilla" class="btn btn-outline-primary btn-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Descargar Plantilla Base
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="importarHistorico" class="space-y-4">
                <div>
                    <label class="form-label block mb-2 font-medium text-gray-700">Archivo Excel a Importar</label>
                    <input type="file" wire:model="archivo" class="form-input" accept=".xlsx,.xls,.csv" required>
                    @error('archivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="btn btn-primary flex items-center gap-2" wire:loading.attr="disabled" wire:target="importarHistorico, archivo">
                        <span wire:loading.remove wire:target="importarHistorico">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </span>
                        <span wire:loading wire:target="importarHistorico">
                            <svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                        <span wire:loading.remove wire:target="importarHistorico">Procesar Histórico</span>
                        <span wire:loading wire:target="importarHistorico">Importando y procesando...</span>
                    </button>
                </div>
            </form>

            @if($resumen)
            <div class="mt-6">
                <h4 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2">Resumen de Importación</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-600 font-semibold">Nuevos</p>
                            <p class="text-2xl font-bold text-green-800">{{ $resumen['nuevos'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">✨</div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-blue-600 font-semibold">Actualizados</p>
                            <p class="text-2xl font-bold text-blue-800">{{ $resumen['actualizados'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">🔄</div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-red-600 font-semibold">Errores</p>
                            <p class="text-2xl font-bold text-red-800">{{ $resumen['errores'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">⚠️</div>
                    </div>
                </div>

                @if(count($erroresImportacion) > 0)
                <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h5 class="text-red-800 font-bold mb-2">Detalle de Errores:</h5>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1 max-h-40 overflow-y-auto">
                        @foreach($erroresImportacion as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>
