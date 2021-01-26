<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Crypt;

class SendUserActivationEmail extends Mailable
{
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

        $activationLink = $siteUrl.'/'.Crypt::encryptString(Carbon::now()->timestamp).'/'.Crypt::encryptString($this->user->id).'/'.Crypt::encryptString($this->user->email);

        return $this->to($this->user->email)
            ->markdown('email.user-activation')
            ->subject($subject)
            ->with(
                [
                    'app' => $app,
                    'subject' => $subject,
                    'name' => $this->user->name,
                    'activationLink' => $activationLink,
                    'mailFrom' => config('mail.from.address'),
                ]
            );
    }
}
