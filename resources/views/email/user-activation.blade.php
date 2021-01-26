@component('mail::message')
# Hi {{ $name }},

Welcome to *{{ $app }}*. Please click the link below to activate your account by verifying your email address.

@component('mail::button', ['url' => $activationLink])
    Verify your email address
@endcomponent

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
