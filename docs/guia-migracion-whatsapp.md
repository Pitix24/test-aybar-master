# Guía de Migración: Angular WhatsApp a Laravel Livewire

Esta guía detalla los pasos para convertir los componentes del proyecto **frontend-whatsapp** (Angular) en componentes **Blade/Livewire** dentro de tu proyecto principal en Laravel.

## 1. Mapeo de Componentes

Para mantener la estructura que ya tienes en Angular, asociaremos cada componente de Angular con un componente de Livewire:

| Componente Angular | Componente Livewire (Laravel) | Ubicación en Laravel |
| :--- | :--- | :--- |
| `lista-chat-izquierda` | `crm.whatsapp.chat-lista` | `app/Livewire/Crm/Whatsapp/ChatLista.php` |
| `mensaje-derecha` | `crm.whatsapp.mensaje-individual` | `app/Livewire/Crm/Whatsapp/MensajeIndividual.php` |
| `cabecera-derecha` | (Integrar en `chat-conversacion`) | `resources/views/livewire/crm/whatsapp/chat-conversacion.blade.php` |
| `chat-input-derecha` | (Integrar en `chat-conversacion`) | `resources/views/livewire/crm/whatsapp/chat-conversacion.blade.php` |

## 2. Proceso de Conversión (Lógica de Reemplazo)

Al mover el HTML de Angular (`.html`) a Blade (`.blade.php`), aplica estos reemplazos:

*   **Iteraciones**: `*ngFor="let item of items"` &rarr; `@foreach($items as $item) ... @endforeach`
*   **Condicionales**: `*ngIf="condicion"` &rarr; `@if($condicion) ... @endif`
*   **Contenedores Invisibles**: `<ng-container>` &rarr; (Eliminar o usar `@if/@foreach` directamente)
*   **Clases Dinámicas**: `[ngClass]="{'clase': condicion}"` &rarr; `{{ $condicion ? 'clase' : '' }}`
*   **Eventos**: `(click)="funcion()"` &rarr; `wire:click="funcion"`
*   **Modelos de Input**: `[(ngModel)]="variable"` &rarr; `wire:model="variable"`
*   **Interpolación**: `{{ variable }}` &rarr; `{{ $variable }}` (Igual, pero añade el `$`)

## 3. Ejemplo Práctico: Lista de Chats

### De Angular (`lista-chat.component.html`):
```html
<div *ngFor="let contacto of contactoItems" (click)="seleccionar(contacto)">
    <h4>{{ contacto.name }}</h4>
</div>
```

### A Livewire (`chat-lista.blade.php`):
```html
@foreach($contactos as $contacto)
    <div wire:click="seleccionar({{ $contacto->id }})" class="{{ $selectedId == $contacto->id ? 'active' : '' }}">
        <h4>{{ $contacto->contacto->nombre }}</h4>
    </div>
@endforeach
```

## 4. Migración de Estilos (CSS)

1.  **Copiar CSS**: Toma el contenido de los archivos `.css` de cada componente de Angular.
2.  **Unificar**: Pégalos en tu archivo de estilos principal de Laravel o crea uno específico: `resources/css/whatsapp.css`.
3.  **Vite**: Asegúrate de que `resources/js/app.js` importe ese CSS o cárgalo en tu `layout-whatsapp.blade.php`.

## 5. Pasos Críticos para el Chat

### Manejo de Multimedia
En Angular, el componente `mensaje-derecha` tiene mucha lógica para mostrar si es imagen, video o audio. Debes replicar el `@if` en Blade:
```html
@if($mensaje->tipo == 'image')
    <img src="{{ $mensaje->contenido }}" class="imagen_mensaje">
@elseif($mensaje->tipo == 'audio')
    <audio src="{{ $mensaje->contenido }}" controls></audio>
@endif
```

### Auto-Scroll
Para que el chat baje automáticamente al recibir un mensaje (que Angular hace con `@ViewChild`), en Livewire usa un script al final de `chat-conversacion.blade.php`:
```javascript
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('mensajeEnviado', () => {
            const container = document.getElementById('chat-container');
            container.scrollTop = container.scrollHeight;
        });
    });
</script>
```

## 6. Siguientes Pasos Recomendados

1.  **Diseño**: Copia el CSS global de `src/styles.css` del proyecto Angular a tu proyecto Laravel.
2.  **Imágenes**: Mueve las imágenes de `src/assets/` a la carpeta `public/assets/` de Laravel.
3.  **Refactor**: Empieza convirtiendo la **Lista de Chats**, ya que es lo más sencillo y te dará la estructura base.
