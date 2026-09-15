<div class="g_panel">
    <x-loading-overlay wire:loading wire:target="archivo, adjuntar, eliminarArchivo" message="Procesando archivo..." />

    @if(!$soloLectura)
        @can('ticket.agregar-archivo')
            <h4 class="g_panel_titulo"><i class="fa-solid fa-cloud-arrow-up"></i> Nuevo Adjunto</h4>

            <div class="formulario">
                <input type="file" id="fileUpload" wire:model="archivo" accept=".pdf,.docx,.xlsx,.pptx,.jpg,.jpeg,.png"
                    style="display: none;">

                <div class="contenedor_dropzone" onclick="document.getElementById('fileUpload').click()"
                    wire:loading.class="g_deshabilitado" wire:target="archivo">
                    @if ($archivo)
                        <div class="dropzone_item">
                            @php
                                $ext = strtolower($archivo->getClientOriginalExtension());
                                $icons = [
                                    'pdf' => 'fa-file-pdf',
                                    'docx' => 'fa-file-word',
                                    'xlsx' => 'fa-file-excel',
                                    'pptx' => 'fa-file-powerpoint',
                                    'jpg' => 'fa-file-image',
                                    'jpeg' => 'fa-file-image',
                                    'png' => 'fa-file-image',
                                ];
                            @endphp

                            <i class="fa-solid {{ $icons[$ext] ?? 'fa-file' }}"></i>
                            <span>{{ $archivo->getClientOriginalName() }}</span>

                            <button type="button" wire:click="$set('archivo', null)" class="dropzone_remove_button">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @else
                        <div wire:loading.remove wire:target="archivo">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Haz clic para subir archivo</p>
                        </div>
                        <div wire:loading wire:target="archivo">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                            <p>Subiendo archivo...</p>
                        </div>
                    @endif
                </div>

                @error('archivo')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror

                @if ($archivo)
                    <div class="g_margin_bottom_10">
                        <label for="descripcion_archivo">Descripción del archivo <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <textarea wire:model="descripcion_archivo" id="descripcion_archivo" rows="2"
                            class="@error('descripcion_archivo') input-error @enderror"></textarea>
                        @error('descripcion_archivo')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones g_margin_bottom_10">
                        <button wire:click="adjuntar" class="g_boton guardar" wire:loading.attr="disabled" wire:target="adjuntar">
                            <span wire:loading.remove wire:target="adjuntar">Adjuntar <i class="fa-solid fa-paperclip"></i></span>
                            <span wire:loading wire:target="adjuntar">Adjuntando... <i
                                    class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                @endif
            </div>
        @endcan
    @endif

    <h4 class="g_panel_titulo">Documentos Adjuntos</h4>
    <div class="g_contenedor_tabla">
        <table class="g_tabla">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="g_celda_centro">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($archivos_existentes as $file)
                    <tr wire:key="file-{{ $file->id }}">
                        <td>
                            <div class="g_negrita">{{ $file->descripcion }}</div>
                            <div>{{ $file->nombre_original }}</div>
                        </td>
                        <td class="g_celda_acciones g_celda_centro">
                            @can('ticket.ver-archivo')
                                <a href="{{ $file->url }}" target="_blank" class="g_accion ver" title="Ver">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            @endcan

                            @if(!$soloLectura)
                                @can('ticket.eliminar-archivo')
                                    <button type="button" onclick="alertaEliminarArchivo({{ $file->id }})" class="g_accion eliminar"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">
                            <div class="g_vacio">
                                <i class="fa-regular fa-face-grin-wink"></i>
                                <p>No hay archivos.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@script
<script>
    window.alertaEliminarArchivo = function (id) {
        Swal.fire({
            title: '¿Eliminar Adjunto?',
            text: "El archivo será borrado permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.eliminarArchivo(id);
            }
        });
    }
</script>
@endscript