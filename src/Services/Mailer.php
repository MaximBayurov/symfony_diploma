<?php

namespace App\Services;

use App\Entity\Subscription;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private static ?Address $fromAddress = null;
    
    public function __construct(
        private EmailVerifier $emailVerifier,
        private MailerInterface $mailer
    ){
    }
    
    public function sendEmailConfirmation($user)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(self::defaultFromAddress())
                ->to($user->getEmail())
                ->subject('Подтверждение Email')
                ->htmlTemplate('mail/confirmation_email.html.twig')
        );
    }
    
    public function sendSubscribe(Subscription $subscription)
    {
        $email = (new TemplatedEmail())
            ->from(self::defaultFromAddress())
            ->to($subscription->getUser()->getEmail())
            ->subject('Уведомление о типе выбранной подписки')
            ->htmlTemplate('mail/subscription.html.twig')
            ->context([
                'subscription' => $subscription
            ])
        ;
        
        $this->mailer->send($email);
    }
    
    private static function defaultFromAddress(): Address
    {
        if (empty(self::$fromAddress)) {
            self::$fromAddress = new Address('mailbot@symfony.diploma.com', 'Mail Bot');
        }
    
        return self::$fromAddress;
    }
}