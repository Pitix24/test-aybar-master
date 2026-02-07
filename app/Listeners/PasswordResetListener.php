<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;

class PasswordResetListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event)
    {
        $event->user->forceFill([
            'password_changed_at' => now(),
            'must_change_password' => false,
            'email_verified_at' => $event->user->email_verified_at ?? now(),
        ])->save();
    }
}
