<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategorieController extends Controller
{
    /**
     * @Route("/{slug}", name="categorie_voir")
     *
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirAction($slug)
    {
        $doctrine = $this
            ->getDoctrine()
        ;

        $categorie = $doctrine
            ->getRepository('AppBundle:Categorie')
            ->findOneBySlug($slug)
        ;

        if (empty($categorie)) {
            throw new NotFoundHttpException('Catégorie non trouvée');
        }

        $findBy = [
            'categorie' => $categorie,
        ];

        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $findBy['published'] = 1;
        }

        $courriers = $doctrine
            ->getRepository('AppBundle:Courrier')
            ->findBy($findBy, [
                'envoi' => 'DESC',
            ])
        ;

        return $this->render('categorie/voir.html.twig', [
            'categorie' => $categorie,
            'courriers' => $courriers,
        ]);
    }


    function menuAction()
    {
        $categories = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Categorie')
            ->findAll()
        ;

        return $this->render('categorie/menu.html.twig', [
            'categories' => $categories,
        ]);
    }
}
