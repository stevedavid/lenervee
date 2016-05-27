<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Presse;
use AppBundle\Form\Admin\PresseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PresseController extends Controller
{
    /**
     * @Route("/admin/extraits-de-presse", name="admin_presse_lister")
     *
     * @return Response
     */
    public function listerAction(Request $request)
    {
        $presses = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Presse')
            ->findBy([], [
                'date' => 'DESC'
            ]);

        return $this->render('admin/presse/lister.html.twig', [
            'presses' => $presses,
            'id' => $request->query->get('id', null),
        ]);
    }

    /**
     * @Route("admin/extraits-de-presse/ajouter/{id}", name="admin_presse_ajouter")
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function ajouterAction(Request $request, $id = null)
    {
        $em = $this
            ->getDoctrine()
            ->getManager()
        ;
        $presse = is_null($id) ? new Presse() : $em
            ->getRepository('AppBundle:Presse')
            ->find($id)
        ;
        $formPresse = $this->createForm(PresseType::class, $presse);

        if ($request->isMethod(Request::METHOD_POST)) {

            $formPresse->handleRequest($request);
            if ($formPresse->isValid()) {
                $presse = $formPresse->getData();

                $em->persist($presse);

                $em->flush();

                return $this->redirect($this->generateUrl('admin_presse_lister', [
                    'id' => $presse->getId(),
                ]));
            } else {
                throw new \Exception($formPresse->getErrors());
            }
        }


        return $this->render('admin/presse/ajouter.html.twig', [
            'presse_form' => $formPresse->createView(),
        ]);
    }

    /**
     * @Route("admin/extraits-de-presse/supprimer/{id}", name="admin_presse_supprimer")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerAction($id)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $presse = $em
            ->getRepository('AppBundle:Presse')
            ->find($id)
        ;

        if (!empty($presse)) {
            $em->remove($presse);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_presse_lister'));
        }

        throw new NotFoundHttpException('Courrier non trouvé');
    }
}