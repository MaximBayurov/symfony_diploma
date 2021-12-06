<?php

namespace App\EventSubscribers;

use App\Entity\FlashMessage;
use App\Events\User\BeforeProfileChangedEvent;
use App\Services\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OnBeforeProfileChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Mailer $mailer,
        private TranslatorInterface $translator,
    ){
    }
    
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
            $this->mailer->sendEmailConfirmation($changed);
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