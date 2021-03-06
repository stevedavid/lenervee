<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TagController extends Controller
{

    /**
     * @Route("/tag/{slug}/", name="tag_voir", requirements={"slug": "[a-zA-Z1-9\-_]+"})
     *
     * @param $slugTag
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirAction($slug)
    {
        $doctrine = $this->getDoctrine();

        /** @var Tag $tag */
        $tag = $doctrine
            ->getRepository('AppBundle:Tag')
            ->findOneBySlug($slug)
        ;

        if (empty($tag)) {
            throw new NotFoundHttpException('Tag non trouvé');
        }

        /** @var Courrier $courriers */
        $courriers = $doctrine
            ->getRepository('AppBundle:Courrier')
            ->findByTag($tag->getId())
        ;

        return $this->render('tag/voir.html.twig', [
            'courriers' => $courriers,
            'tag' => $tag,
        ]);
    }
}