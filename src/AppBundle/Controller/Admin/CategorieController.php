<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Reaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategorieController extends Controller
{
    /**
     * @Route("/admin/categories/", name="admin_categorie_lister")
     *
     * @return Response
     */
    public function listerAction()
    {
           $categories = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->findAll()
        ;

        return $this->render('admin/categorie/lister.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin/categories/editer/", name="admin_categorie_editer")
     *
     * @param Request $request
     * @return Response
     */
    public function editerAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $id = $request->request->get('id');
            $value = $request->request->get('value');

            $categorie = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Categorie')
                ->find($id)
            ;

            $categorie->setDescription($value);

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

            return new JsonResponse(null, 200);
        }

        return new Response(null, 405);
    }
}