<?php

namespace App\Events;

use App\Entity\FlashMessage;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

abstract class UserEvent extends Event
{
    private array $flashMessages;
    
    public function __construct(
        protected User $user
    ) {
    
    }
    
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    
    /**
     * @return array
     */
    public function getFlashMessages(): array
    {
        return $this->flashMessages;
    }
    
    /**
     * @param FlashMessage $flashMessages
     */
    public function setFlashMessages(FlashMessage $flashMessages): void
    {
        $this->flashMessages[] = $flashMessages;
    }
}