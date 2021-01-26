<?php

namespace App\Events;

use App\Models\User;

class UserRegistered
{
    public User $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
