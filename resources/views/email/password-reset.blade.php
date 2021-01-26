@component('mail::message')
# Hello,

You are receiving this email because we received a password reset request for your account.

@component('mail::button', ['url' => $resetLink])
    Reset Password
@endcomponent

This password reset link will expire in 60 minutes.
If you did not request a password reset, no further action is required.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent

