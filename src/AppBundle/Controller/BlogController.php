<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Courrier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    const MAIL_TITLE = '%s vous conseille un courrier sur http://lenervee.com/ !';

    /**
     * @Route("/blog/page/contactez-nous", name="blog_contact")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction()
    {
        return $this->render('blog/page/contact.html.twig');
    }

    /**
     * @Route("/blog/page/mentions-legales", name="blog_mentions")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mentionsLegalesAction()
    {
        return $this->render('blog/page/mentions_legales.html.twig');
    }

    /**
     * @Route("/blog/page/a-propos", name="blog_about")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aProposAction()
    {
        return $this->render('blog/page/a_propos.html.twig');
    }

    /**
     * @Route("/feed", name="blog_feed")
     *
     * @return Response
     */
    public function feedAction()
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findAll()
        ;

        $feed = $this->get('eko_feed.feed.manager')->get('courrier');
        $feed->addFromArray($courriers);

        return new Response($feed->render('rss'));
    }

    /**
     * Not routable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function metaAction($courrier)
    {
        return $this->render('blog/meta.html.twig', [
            'courrier' => $courrier,
        ]);
    }

    /**
     * Not routable
     *
     * @param Courrier $courrier
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function partageAction(Courrier $courrier)
    {
        return $this->render('blog/partage.html.twig', [
            'courrier' => $courrier,
        ]);
    }

    /**
     * @Route("/blog/email/rediger", name="blog_email")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function emailAction(Request $request)
    {
        return $this->render('blog/partage/form.html.twig', [
            'courrier' => $request->query->get('courrier'),
        ]);
    }

    /**
     * @Route("/blog/email/envoyer", name="blog_email_send")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function emailSendAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {

            $senderEmail = $request->request->get('senderEmail');
            $senderName = $request->request->get('senderName');
            $friendEmail = $request->request->get('friendEmail');

            $errors = [];
            switch(true) {
                case !filter_var($senderEmail, FILTER_VALIDATE_EMAIL) || empty($senderEmail):
                    $errors['senderEmail'] = 'empty';
                case empty($senderName):
                    $errors['senderName'] = 'empty';
                case !filter_var($friendEmail, FILTER_VALIDATE_EMAIL) || empty($friendEmail):
                    $errors['friendEmail'] = 'empty';
            }

            if (count($errors)) {
                return new JsonResponse($errors, 412);
            }

            if ($this->get('kernel')->getEnvironment() == 'prod') {
                $email = \Swift_Message::newInstance()
                    ->setSubject(sprintf(self::MAIL_TITLE, $senderName))
                    ->setFrom([$senderEmail => $senderName])
                    ->setTo($friendEmail)
                    ->setBody($this->renderView('blog/partage/email.html.twig', [
                        'courrier' => $this->getDoctrine()->getRepository('AppBundle:Courrier')->find($request->request->get('id')),
                        'sender_name' => $senderName,
                    ]),'text/html')
                ;

                $deliveredTo = $this->get('mailer')->send($email);

                if ($deliveredTo) {
                    return new JsonResponse(null, 200);

                }
                return new JsonResponse(null, 500);

            }
            return new JsonResponse(null, 200);

        }
        return new Response(null, 405);
    }

    /**
     * @Route("/export/pdf")
     *
     * @return Response
     */
    public function exportPdfAction()
    {
        return $this->render('admin/courrier/exporter.html.twig', [
            'courriers' => $this->getDoctrine()->getRepository('AppBundle:Courrier')->findAll(),
        ]);
    }
}