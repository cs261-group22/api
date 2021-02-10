<?php

namespace App\Providers;

use App\Events\PasswordRecoveryRequested;
use App\Events\UserReferred;
use App\Listeners\SendPasswordRecoveryLink;
use App\Listeners\SendUserEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserReferred::class => [
            SendUserEmailVerificationNotification::class,
        ],

        PasswordRecoveryRequested::class => [
            SendPasswordRecoveryLink::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
