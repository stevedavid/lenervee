<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Reaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class IndexController extends Controller
{
    const ACTIVITE_COURRIER = '%s# | Le courrier "<em>%s</em>" a été publié.';
    const ACTIVITE_REACTION = '%s# | <strong>%s</strong> a commenté sur "<em>%s</em>".';
    /**
     * @Route("/admin", name="admin_index_index")
     *
     * @return Response
     */
    public function indexAction()
    {
        $blocksHeader = [
            'courriers' => count($this
                ->getDoctrine()
                ->getRepository('AppBundle:Courrier')
                ->findAll([
                    'published' => 1,
                ])),
            'reactions' => count($this
                ->getDoctrine()
                ->getRepository('AppBundle:Reaction')
                ->findAll([
                    'status' => Reaction::STATUS_ACCEPTED,
                ])),
            'tags' => count($this
                ->getDoctrine()
                ->getRepository('AppBundle:Tag')
                ->findAll()),
            'categories' => $this
                ->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->findAll(),
        ];

        return $this->render('admin/admin/index.html.twig', [
            'blocks_header' => $blocksHeader,
        ]);
    }

    /**
     * Not routable
     */
    function latestsCourriersAction()
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findLast(4)
        ;

        return $this->render('admin/admin/index/derniers-articles.html.twig', [
            'courriers' => $courriers,
        ]);
    }

    /**
     * Not routable
     */
    function latestsReactionsAction()
    {
        $pendingReactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findBy([
                'status' => Reaction::STATUS_PENDING,
            ], [
                'id' => 'DESC',
            ])
        ;

        $acceptedReactions  = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findBy([
                'status' => Reaction::STATUS_ACCEPTED,
            ], [
                'id' => 'DESC',
            ])
        ;

        return $this->render('admin/admin/index/dernieres-reactions.html.twig', [
            'pending_reactions' => array_slice($pendingReactions, 0, 5),
            'accepted_reactions' => array_slice($acceptedReactions, 0, 5),
        ]);
    }

    /**
     * Not routable
     */
    function categoriesStatsAction()
    {
        $categories = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->findAll()
        ;

        return $this->render('admin/admin/index/categories-stats.html.twig', [
            'categories' => $categories,
        ]);
    }

    function lastActivityAction()
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findAll();

        $reactions = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findAll();

        $max = max(
            count($courriers),
            count($reactions)
        );

        $i = 0;
        while($i < $max) {

            if (isset($courriers[$i])) {
                $elements[$courriers[$i]->getEnvoi()->getTimestamp()] = [
                    'label' => sprintf(
                        self::ACTIVITE_COURRIER,
                        $courriers[$i]->getId(),
                        $courriers[$i]->getName()
                    ),
                    'icon' => 'envelope-o',
                    'class' => 'danger',
                ];
            }

            if(isset($reactions[$i])) {
                $elements[$reactions[$i]->getDate()->getTimestamp()] = [
                    'label' => sprintf(
                        self::ACTIVITE_REACTION,
                        $reactions[$i]->getId(),
                        $reactions[$i]->getName(),
                        $reactions[$i]->getCourrier()->getName()
                    ),
                    'icon' => 'comments',
                    'class' => 'info'
                ];
            }

            ++$i;
        }

        krsort($elements);

        return $this->render('admin/admin/index/derniere-activite.html.twig', [
            'elements' => $elements,
        ]);
    }

    /**
     * Not routable
     */
    function blogrollAction()
    {
        $yamlPath = $this->get('kernel')->getRootDir() . '/../' . $this->getParameter('blogroll_yml_path');

        return $this->render('admin/admin/index/blogroll.html.twig', [
            'liens' => (new Parser)->parse(file_get_contents($yamlPath))['blogroll'],
        ]);
    }
}