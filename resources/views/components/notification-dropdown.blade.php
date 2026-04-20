@props(['unreadCount', 'notifications'])

<div class="header_notifications_dropdown" 
     x-show="open" 
     x-cloak 
     @click.outside="if(open) open = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100">
    
    <div class="header_notifications_header">
        <span>Notificaciones</span>
        @if ($unreadCount > 0)
            <button wire:click="marcarTodasComoLeidas" class="header_notifications_mark_all">
                Marcar todas como leídas
            </button>
        @endif
    </div>

    <div class="header_notifications_list">
        @forelse ($notifications as $notification)
            <div class="header_notification_item">
                <div class="header_notification_content">
                    <p class="header_notification_title">{{ $notification->data['asunto'] ?? 'Actualización de Ticket' }}</p>
                    <p class="header_notification_text">{{ $notification->data['mensaje'] ?? '' }}</p>
                    <small class="header_notification_time">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <div class="header_notification_actions">
                    <a href="{{ $notification->data['url'] ?? '#' }}" wire:click="marcarComoLeida('{{ $notification->id }}')" class="header_notification_link">
                        Ver
                    </a>
                    <button wire:click="marcarComoLeida('{{ $notification->id }}')" class="header_notification_check" title="Marcar como leída">
                        <i class="fa-solid fa-check"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="header_notification_empty">
                No tienes notificaciones pendientes
            </div>
        @endforelse
    </div>
</div>
