<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();

        $user = $passport->getUser();
        if(!$user->isIsVerifyed()){
            throw  new CustomUserMessageAuthenticationException('Моля верифицирайте акаунта си преди влизане!');
        }

    }
    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10]
        ];
    }
}