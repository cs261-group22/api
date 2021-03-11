<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordRecoveryRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $email;

    /**
     * Create a new event instance.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
