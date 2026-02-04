@props([
    'message' => 'Cargando información…'
])

<div class="contenedor_spiner_load" {{ $attributes }}>
    <div class="spinner_box">
        <div class="spinner_icon"></div>
        <span class="spinner_text">{{ $message }}</span>
    </div>
</div>