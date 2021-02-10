@component('mail::message')
# Hi,

You have been invited to join {{ $app }}. Please click the link below to activate your account.

@component('mail::button', ['url' => $activationLink])
    Activate Account
@endcomponent

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
