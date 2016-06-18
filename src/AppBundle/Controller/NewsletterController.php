<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterController extends Controller
{
    /**
     * @Route("/blog/newsletter/{uid}", name="newsletter_respond")
     *
     * @param Request $request
     */
    public function respondAction(Request $request, $uid)
    {
        $connection = $this->getDoctrine()->getConnection();
        $sql = '
            SELECT uid, email
            FROM `nl_candidate`
            WHERE uid = :uid
        ';

        exit;
    }
}
