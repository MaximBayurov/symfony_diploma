<?php

namespace App\EventSubscribers\User;

use App\Entity\FlashMessage;
use App\Events\User\RegisterEvent;
use App\EventSubscribers\OnUserEventSubscriber;

class OnRegisterSubscriber extends  OnUserEventSubscriber
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RegisterEvent::class => 'sendVerifyEmail'
        ];
    }
    
    public function sendVerifyEmail(RegisterEvent $event)
    {
        $user = $event->getUser();
    
        $this->sendEmailConfirmation($user);
    
        $event->setFlashMessages(new FlashMessage(
            $this->translator->trans('Confirm your email to complete registration')
        ));
    }
}