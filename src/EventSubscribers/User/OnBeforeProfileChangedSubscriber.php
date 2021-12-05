<?php

namespace App\EventSubscribers\User;

use App\Entity\FlashMessage;
use App\Events\User\BeforeProfileChangedEvent;
use App\EventSubscribers\OnUserEventSubscriber;

class OnBeforeProfileChangedSubscriber extends  OnUserEventSubscriber
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeProfileChangedEvent::class => 'onBeforeProfileChangedEvent'
        ];
    }
    
    public function onBeforeProfileChangedEvent(BeforeProfileChangedEvent $event)
    {
        $changed = $event->getUser();
        $current = $event->getCurrentUser();
        if ($changed->getEmail() !== $current->getEmail()) {
            $this->sendEmailConfirmation($changed);
            $event->setFlashMessages(new FlashMessage(
                $this->translator->trans('Confirm your email to change it')
            ));
            $changed->setEmail(
                $current->getEmail()
            );
        }
        
        $event->setFlashMessages(new FlashMessage(
            $this->translator->trans('Profile changed successfully')
        ));
    }
}