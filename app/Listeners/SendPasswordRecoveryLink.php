<?php

namespace App\Listeners;

use App\Events\PasswordRecoveryRequested;
use App\Mail\SendPasswordRecoveryEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendPasswordRecoveryLink implements ShouldQueue
{
    /**
     * Dispatches an email with a password recovery link.
     *
     * @param PasswordRecoveryRequested $event
     * @return void
     */
    public function handle(PasswordRecoveryRequested $event)
    {
        Mail::send(new SendPasswordRecoveryEmail($event->email));
    }
}
