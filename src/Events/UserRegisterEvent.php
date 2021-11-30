<?php

namespace App\Events;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    public function __construct(
        private User $user
    ) {
    
    }
    
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}