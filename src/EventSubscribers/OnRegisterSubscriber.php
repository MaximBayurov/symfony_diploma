<?php

namespace App\EventSubscribers;

use App\Entity\FlashMessage;
use App\Events\User\RegisterEvent;
use App\Services\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OnRegisterSubscriber implements EventSubscriberInterface
{
    
    public function __construct(
        private Mailer $mailer,
        protected TranslatorInterface $translator,
    ){
    }
    
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
    
        $this->mailer->sendEmailConfirmation($user);
    
        $event->setFlashMessages(new FlashMessage(
            $this->translator->trans('Confirm your email to complete registration')
        ));
    }
}