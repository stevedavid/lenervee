<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Courrier;
use AppBundle\Entity\Reaction;
use AppBundle\Entity\Tag;
use AppBundle\Form\ReactionType;
use AppBundle\Repository\CourrierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Parser;

class WidgetController extends Controller
{
    /**
     * Not routable
     *
     * @param $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function defautAction($template)
    {
        return $this->render('widget/' . $template . '.html.twig');
    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function populairesAction()
    {

        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findMostLiked(20);

        return $this->render('widget/populaires.html.twig', [
            'courriers' => $courriers,
        ]);

    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function commenteesAction()
    {

        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findMostCommented(20);

        return $this->render('widget/commentees.html.twig', [
            'courriers' => $courriers,
        ]);

    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function blogrollAction()
    {
        $yamlPath = $this->get('kernel')->getRootDir() . '/../' . $this->getParameter('blogroll_yml_path');

        return $this->render('widget/blogroll.html.twig', [
            'liens' => (new Parser)->parse(file_get_contents($yamlPath))['blogroll'],
        ]);

    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function presseAction()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Presse')
            ->findAll();

        return $this->render('widget/presse.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function reponsesAction()
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findCourrierAvecReponse();

        return $this->render('widget/reponses.html.twig', [
            'courriers' => $courriers,
        ]);
    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function derniereReactionAction()
    {
        $reaction = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Reaction')
            ->findOneBy([
                'status' => Reaction::STATUS_ACCEPTED,
            ], [
                'id' => 'DESC'
            ]);

        return $this->render('widget/derniere_reaction.html.twig', [
            'reaction' => $reaction,
        ]);
    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function tagsAction()
    {
        $aTags = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findMostPopular(30);

        $colors = [
            '#555555',
            '#565b62',
            '#57626f',
            '#5f85b0',
        ];

        shuffle($aTags);
        $tags = [];
        foreach ($aTags as $value) {
            $tags[$value['slug']] = $value['nbCourriers'];
        }

        foreach ($tags as $tag => $nbCourriers) {
            $fontSizes[$tag] = 10 + (($nbCourriers - min($tags)) * 5);
        }

        return $this->render('widget/tags.html.twig', [
            'tags' => $tags,
            'sizes' => $fontSizes,
            'colors' => $colors,
        ]);
    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function statistiquesAction()
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $nbCourriers = $em
            ->getRepository('AppBundle:Courrier')
            ->count();

        $nbReponses = $em
            ->getRepository('AppBundle:Courrier')
            ->count([
                'reponse' => CourrierRepository::NOT_NULL,
            ]);

        return $this->render('widget/statistiques.html.twig', [
            'nb_courriers' => $nbCourriers,
            'nb_reponses' => $nbReponses,
        ]);
    }
}