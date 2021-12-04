<?php

namespace App\EventSubscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class OnSecurityAuthenticationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            LoginFailureEvent::class => 'onSecurityAuthenticationFailure'
        ];
    }
    
    public function onSecurityAuthenticationFailure(LoginFailureEvent $event): void
    {
        $request = $event->getRequest();
        $request->getSession()->set(
            '_remember_me',
            $request->request->get('_remember_me')
        );
    }
    
}