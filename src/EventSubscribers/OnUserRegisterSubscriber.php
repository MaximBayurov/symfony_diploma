<?php

namespace App\EventSubscribers;

use App\Events\UserRegisterEvent;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class OnUserRegisterSubscriber implements EventSubscriberInterface
{
    private EmailVerifier $emailVerifier;
    
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisterEvent::class => 'sendVerifyEmail'
        ];
    }
    
    public function sendVerifyEmail(UserRegisterEvent $event)
    {
    
        $user = $event->getUser();
    
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('mailbot@symfony.diploma.com', 'Mail Bot'))
                ->to($user->getEmail())
                ->subject('Подтверждение Email')
                ->htmlTemplate('mail/confirmation_email.html.twig')
        );
    }
}