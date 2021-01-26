<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Crypt;

class SendPasswordRecoveryEmail extends Mailable
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Builds the email template.
     *
     * @return SendPasswordRecoveryEmail
     */
    public function build()
    {
        $app = config('app.name');
        $subject = trans(':app Password Recovery Request', ['app' => $app]);
        $siteUrl = config('cs261.pwa.url').config('cs261.pwa.email_password_reset');

        $resetLink = $siteUrl.'/'.Crypt::encryptString(Carbon::now()->timestamp).'/'.Crypt::encryptString($this->email);

        return $this->to($this->email)
            ->view('email.password-reset')
            ->subject($subject)
            ->with(
                [
                    'app' => $app,
                    'subject' => $subject,
                    'resetLink' => $resetLink,
                    'mailFrom' => config('mail.from.address'),
                ]
            );
    }
}
