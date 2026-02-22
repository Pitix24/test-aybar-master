<?php

namespace App\Livewire\Crm\Whatsapp;

use Livewire\Component;
use Livewire\Attributes\Layout;
#[Layout('layouts.layout-whatsapp')]
class ChatContainer extends Component
{
    public function render()
    {
        return view('livewire.crm.whatsapp.chat-container');
    }
}
