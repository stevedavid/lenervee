<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Image;
use AppBundle\Form\Admin\CourrierType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CourrierController extends Controller
{
    /**
     * @Route("/admin/courriers", name="admin_courrier_lister")
     *
     * @return Response
     */
    public function listerAction(Request $request)
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findBy([], [
                'id' => 'DESC'
            ]);

        return $this->render('admin/courrier/lister.html.twig', [
            'courriers' => $courriers,
            'id' => $request->query->get('id', null),
        ]);
    }

    /**
     * @Route("/admin/courriers/supprimer/{id}", name="admin_courrier_supprimer")
     *
     * @return Response
     */
    public function supprimerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $courrier = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->find($id);

        $em->remove($courrier);

        $em->flush();

        return $this->redirect($this->generateUrl('admin_courrier_lister'));
    }

    /**
     * @Route("/admin/courriers/rediger/{id}", name="admin_courrier_rediger")
     *
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function redigerAction(Request $request, $id = null)
    {
        $em = $this
            ->getDoctrine()
            ->getManager()
        ;
        $courrier = is_null($id) ? new Courrier() : $em
            ->getRepository('AppBundle:Courrier')
            ->find($id)
        ;

        $tags = $courrier->getTags();

        $formCourrier = $this->createForm(CourrierType::class, $courrier);

        foreach ($tags as $tag) {
            $courrier->removeTag($tag);
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $formCourrier->handleRequest($request);

            if ($formCourrier->isValid()) {
                foreach ($tags as $tag) {
                    $courrier->addTag($tag);
                    $tag->addCourrier($courrier);
                    $em->persist($tag);
                }
                $courrier = $formCourrier->getData();
                $image = $courrier->getImage();
                if (!is_null($image)) {
                    $courrier->setImage($image);
                    $em->persist($image);

                    $em->persist($courrier);

                    $em->flush();

                    return $this->redirect($this->generateUrl('admin_courrier_lister', [
                        'id' => $courrier->getId(),
                    ]));
                } else {
                    $formCourrier->addError(new FormError('Vous ne pouvez pas créer un courrier sans image.'));
                }

            } else {
                dump((string) $formCourrier->getErrors(true, false));
                die(__LINE__);
            }
        }

        return $this->render('admin/courrier/rediger.html.twig', [
            'courrier_form' => $formCourrier->createView(),
        ]);
    }

    /**
     * @Route("admin/courriers/_upload", name="admin_courrier_upload")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImageAction(Request $request)
    {
        $dirCourriersImages = __DIR__ . '/../../../../web/' . Image::UPLOAD_DIR;
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->all()['courrier']['image']['file'];

        if (!in_array($uploadedFile->getClientOriginalExtension(), ['png', 'jpg', 'jpeg'])) {
            return new JsonResponse('Format de fichier non autorisé', 500);
        }

        $uploadedFile->move($dirCourriersImages, $uploadedFile->getClientOriginalName());

        return new JsonResponse([
            'path' => Image::UPLOAD_DIR . '/' . $uploadedFile->getClientOriginalName(),
        ], 200);
    }
}