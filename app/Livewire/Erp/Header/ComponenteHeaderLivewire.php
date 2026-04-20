<?php

namespace App\Livewire\Erp\Header;

use Livewire\Component;

class ComponenteHeaderLivewire extends Component
{
    public function getNotificationsProperty()
    {
        return auth()->user()->unreadNotifications()->take(10)->get();
    }

    public function getUnreadCountProperty()
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function marcarComoLeida($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function marcarTodasComoLeidas()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.erp.header.componente-header-livewire');
    }
}
