<?php

namespace App\Events;

class PasswordRecoveryRequested
{
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
