<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Presse;
use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\PresseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TagController extends Controller
{
    /**
     * @Route("/admin/tags/", name="admin_tag_lister")
     *
     * @return Response
     */
    public function listerAction(Request $request)
    {
        $tags = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->findBy([], [
                'name' => 'ASC'
            ]);

        return $this->render('admin/tag/lister.html.twig', [
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/admin/tags/supprimer/", name="admin_tag_supprimer")
     *
     * @return Response
     */
    public function supprimerAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $tag = $doctrine
                ->getRepository('AppBundle:Tag')
                ->find($request->request->get('id'))
            ;

            $em->remove($tag);

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

    /**
     * @Route("/admin/tags/ajouter/", name="admin_tag_ajouter")
     *
     * @return Response
     */
    public function ajouterAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $doctrine = $this->getDoctrine();
            $em = $doctrine->getManager();

            $tag = (new Tag)
                ->setName($request->request->get('tag'))
                ->setSlug(Tag::slugify($request->request->get('tag')))
            ;

            $em->persist($tag);
            $em->flush();

            return new JsonResponse([
                'id' => $tag->getId(),
            ]);
        }
        return new Response(null, 405);
    }
}