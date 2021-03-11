<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class SendUserActivationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the email template.
     *
     * @return $this
     */
    public function build()
    {
        $app = config('app.name');
        $subject = trans(':app Account Activation', ['app' => $app]);
        $siteUrl = config('cs261.pwa.url').config('cs261.pwa.email_verification_path');

        $token = Crypt::encryptString($this->user->id).'/'.
                 Crypt::encryptString($this->user->email).'/'.
                 Crypt::encryptString(Carbon::now()->timestamp);

        $activationLink = "${siteUrl}?token=".base64_encode($token);

        return $this->to($this->user->email)
            ->markdown('email.user-activation')
            ->subject($subject)
            ->with(
                [
                    'app' => $app,
                    'subject' => $subject,
                    'activationLink' => $activationLink,
                    'mailFrom' => config('mail.from.address'),
                ]
            );
    }
}
