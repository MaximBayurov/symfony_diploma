<?php

namespace App\Events\User;

use App\Entity\User;
use App\Events\UserEvent;

class BeforeProfileChangedEvent extends UserEvent
{
    public function __construct(
        protected User $changedUser,
        protected User $currentUser
    ) {
        parent::__construct($changedUser);
    }
    
    /**
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->currentUser;
    }
}