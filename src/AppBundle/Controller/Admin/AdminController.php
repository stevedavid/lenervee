<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Reaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("admin/configuration/idle", name="admin_admin_idle")
     *
     */
    public function idleAction()
    {

        return new Response('OK', 200);
    }

    /**
     * @Route("/admin/_modal", name="admin_admin_modal")
     *
     * @param Request $request
     * @return Response
     */
    public function modalAction(Request $request)
    {
        return $this->render('admin/admin/modal.html.twig', [
            'type' => $request->query->get('type'),
            'message' => $request->query->get('message'),
        ]);
    }

    /**
     * @Route("/admin/_opcachereset", name="admin_admin_opcacheReset")
     *
     * @param Request $request
     * @return Response
     */
    public function resetOpcacheAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {

            $state = opcache_reset();

            if (!$state) {
                return new Response(null, 500);
            }

            return new Response();
        }
    }

    /**
     * Not routable
     */
    function headerAction()
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findByPublished(0, [
                'envoi' => 'DESC'
            ]);

        $reactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findByStatus(Reaction::STATUS_PENDING, [
                'date' => 'DESC'
            ]);

        return $this->render('admin/admin/header.html.twig', [
            'unpublished_courriers' => $courriers,
            'pending_reactions' => $reactions,
        ]);
    }

    /**
     * Not routable
     */
    function sidebarAction()
    {
        $request = $this
            ->get('request_stack')
            ->getMasterRequest()
        ;

        $controller = $request
            ->get('_controller')
        ;
        $controller = explode('\\', $controller);
        $controller = explode('::', array_pop($controller));
        $action = substr($controller[1], 0, -6);
        $controller = substr($controller[0], 0, -10);

        $reactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findAll();

        return $this->render('admin/admin/sidebar.html.twig', [
            'controller' => strtolower($controller),
            'action' => $action,
            'pending_reactions' => array_filter($reactions, function($reaction) {
                return $reaction->getStatus() == Reaction::STATUS_PENDING;
            }),
            'trashed_reactions' => array_filter($reactions, function($reaction) {
                return $reaction->getStatus() == Reaction::STATUS_TRASHED;
            }),
            'debug' => $request->query->get('debug')
        ]);
    }

    /**
     * Not routable
     */
    function quickSidebarAction()
    {
        return $this->render('admin/admin/quick-sidebar.html.twig');
    }
}