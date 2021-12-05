<?php

namespace App\EventSubscribers;

use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class OnUserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EmailVerifier $emailVerifier,
        protected TranslatorInterface $translator,
    ){
    }
    
    protected function sendEmailConfirmation($user)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('mailbot@symfony.diploma.com', 'Mail Bot'))
                ->to($user->getEmail())
                ->subject('Подтверждение Email')
                ->htmlTemplate('mail/confirmation_email.html.twig')
        );
    }
}