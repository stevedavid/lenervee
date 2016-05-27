<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Reaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReactionController extends Controller
{
    const LABELS = [
        Reaction::STATUS_ACCEPTED => [
            'label' => 'Acceptée',
            'class' => 'green',
            'icon' => 'check',
        ],
        Reaction::STATUS_MODERATED => [
            'label' => 'Modérée',
            'class' => 'yellow',
            'icon' => 'ban',
        ],
        Reaction::STATUS_PENDING => [
            'label' => 'En attente',
            'class' => 'default',
            'icon' => 'refresh',
        ],
        Reaction::STATUS_TRASHED => [
            'label' => 'Supprimée',
            'class' => 'red',
            'icon' => 'trash-o',
        ],
    ];

    /**
     * @Route("/admin/reactions/", name="admin_reaction_lister")
     *
     * @return Response
     */
    public function listerAction(Request $request)
    {
        $id = $request->query->get('id');
        $parameters = [];

        if (!is_null($id)) {
            $parameters = [
                'courrier' => $id,
            ];
        }

        $reactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findBy($parameters, [
                'date' => 'DESC'
            ]);

        if (empty($reactions)) {
            throw new NotFoundHttpException('Aucune réaction à montrer.');
        }

        return $this->render('admin/reaction/lister.html.twig', [
            'reactions' => $reactions,
            'labels' => self::LABELS,
            'action' => 'lister',
            'statuses' => array_flip((new \ReflectionClass(Reaction::class))->getConstants()),
            'courrier' => is_null($id) ? null : $reactions[0]->getCourrier()->getName(),
        ]);
    }

    /**
     * @Route("/admin/reactions/moderer/", name="admin_reaction_moderer")
     *
     * @return Response
     */
    public function modererAction()
    {
        $reactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findBy([
                'status' => Reaction::STATUS_PENDING,
            ], [
                'courrier' => 'ASC',
                'id' => 'ASC',
            ])
        ;

        return $this->render('admin/reaction/moderer.html.twig', [
            'reactions' => $reactions,
        ]);

    }

    /**
     * @Route("admin/reactions/corbeille/", name="admin_reaction_corbeille")
     *
     * @return Response
     */
    public function corbeilleAction()
    {
        $reactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findBy([
                'status' => Reaction::STATUS_TRASHED,
            ], [
                'id' => 'ASC',
            ])
        ;

        return $this->render('admin/reaction/lister.html.twig', [
            'reactions' => $reactions,
            'action' => 'corbeille',
            'labels' => self::LABELS,
            'statuses' => array_flip((new \ReflectionClass(Reaction::class))->getConstants()),
        ]);
    }

    /**
     * @Route("/admin/reactions/editer/", name="admin_reaction_editer")
     *
     * @param Request $request
     * @return Response
     */
    public function editerAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $id = $request->request->get('id');
            $value = $request->request->get('value');
            $type = $request->request->get('type');
            $constants = [];
            $labels = null;

            switch ($type) {
                case 'content':
                    $method = 'setReaction';
                    break;
                case 'status':
                    $method = 'setStatus';
                    $constants = array_flip((new \ReflectionClass(Reaction::class))->getConstants());
                    $labels = self::LABELS;
                    break;
                default:
                    throw new \InvalidArgumentException();
                    break;
            }

            $reaction = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Reaction')
                ->find($id)
            ;

            $reaction->$method($value);

            try {
                $this
                    ->getDoctrine()
                    ->getManager()
                    ->flush()
                ;
            } catch(\Exception $e) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], 500);
            }

            return new JsonResponse([
                'constants' => $constants,
                'labels' => $labels,
            ], 200);
        }

        return new Response(null, 405);
    }

    /**
     * @Route("admin/reaction/supprimer/url/", name="admin_reaction_remove_url")
     *
     * @param Request $request
     * @return Response
     */
    public function removeUrlAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $reaction = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Reaction')
                ->find($request->request->get('id'))
            ;

            $reaction->setUrl('');

            try {
                $this
                    ->getDoctrine()
                    ->getManager()
                    ->flush();
            } catch(\Exception $e) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], 500);
            }

            return new Response();
        }

        return new Response(null, 405);
    }

    /**
     * @Route("admin/reactions/supprimer/", name="admin_reaction_supprimer")
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function supprimerAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $reaction = $doctrine
                ->getRepository('AppBundle:Reaction')
                ->find($request->request->get('id'))
            ;

            $em->remove($reaction);

            try {
                $em->flush();
            } catch(\Exception $e) {
                return new JsonResponse([
                    'error' => $e->getMessage(),
                ], 500);
            }

            return new Response();
        }

        return new Response(null, 405);
    }
}