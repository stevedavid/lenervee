<?php

namespace AppBundle\Event\Listener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LogoutListener implements LogoutSuccessHandlerInterface {

    private $tokenStorage;
    private $router;
    private $em;

    public function __construct(TokenStorage $tokenStorage, Router $router, EntityManager $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->em = $em;
    }

    public function onLogoutSuccess(Request $request)
    {
        $session = $this
            ->em
            ->getRepository('AppBundle:Admin\Session')
            ->findOneBySessionToken($request->getSession()->getId());

        $this->em->remove($session);
        $this->em->flush();

        $response = new RedirectResponse($this->router->generate('courrier_index'));
        $response->headers->clearCookie('remember_me');
        $response->headers->clearCookie('session');
        $response->send();

        $request->getSession()->invalidate();

        return $response;
    }
}