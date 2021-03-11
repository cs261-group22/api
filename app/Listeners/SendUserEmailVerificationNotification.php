<?php

namespace App\Listeners;

use App\Mail\SendUserActivationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendUserEmailVerificationNotification implements ShouldQueue
{
    /**
     * Dispatches an email with an account verification link.
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            Mail::send(new SendUserActivationEmail($event->user));
        }
    }
}
