<?php

namespace AppBundle\Event\Listener;

use AppBundle\Entity\Admin\Session;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginListener implements EventSubscriberInterface
{
    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $currentSession = $event->getRequest()->getSession();


        $session = (new Session)
            ->setUser($user)
            ->setSessionToken($currentSession->getId())
        ;

        $this->em->persist($session);
        $this->em->flush();
    }
}