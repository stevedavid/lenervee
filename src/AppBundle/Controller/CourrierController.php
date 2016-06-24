<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Courrier;
use AppBundle\Entity\Reaction;
use AppBundle\Form\ReactionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CourrierController extends Controller
{

    /**
     * @Route("/", name="courrier_index")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $courriers = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->findLast(2, null, $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
        ;

        return $this->render('courrier/index.html.twig', [
            'courriers' => $courriers,
        ]);
    }

    /**
     * @Route("/{slugCategorie}/{slugCourrier}/", name="courrier_voir", requirements={"categorie_slug": "[a-zA-Z1-9\-_]+", "courrier_slug", "[a-zA-Z1-9\-_]+"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function voirAction(Request $request, $slugCategorie, $slugCourrier)
    {
        $sent = $request->query->get('send', null);
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $repoCourrier = $doctrine->getRepository('AppBundle:Courrier');

        $courrier = $repoCourrier->findOneBySlugWithReactionsFiltered(
            $slugCourrier,
            Reaction::STATUS_ACCEPTED
        );

        $is404 = [
            'notFound' => empty($courrier),
            'wrongSlug' => !empty($courrier) && $courrier->getCategorie()->getSlug() != $slugCategorie,
            'denied' => !empty($courrier) && (!$courrier->getPublished() && !$isAdmin),
        ];

        if ($is404['notFound'] || $is404['wrongSlug'] || $is404['denied']) {
            throw new NotFoundHttpException('Courrier non trouvé.');
        }

        $reaction = !$isAdmin ? new Reaction() : (new Reaction)
            ->setName('Administrateur')
            ->setUrl('http://lenervee.com/')
            ->setEmail('admin@lenervee.com')
        ;
        $formReaction = $this->createForm(ReactionType::class, $reaction);

        if ($request->isMethod('POST')) {
            $formReaction->handleRequest($request);
            $nickname = $formReaction['name']->getData();
            if (!$isAdmin && $nickname == 'Administrateur') {
                $formReaction->addError(new FormError('Vous ne pouvez pas utiliser cette valeur pour pseudo.'));
            }
            if ($formReaction->isValid()) {
                /** @var Reaction $reaction */
                $reaction = $formReaction->getData();
                if ($isAdmin) {
                    $reaction->setStatus(Reaction::STATUS_ACCEPTED);
                }
                $reaction->setCourrier($courrier);
                $em->persist($reaction);
                $em->flush();


                return $this->redirect($this->generateUrl('courrier_voir', [
                    'slugCategorie' => $courrier->getCategorie()->getSlug(),
                    'slugCourrier' => $courrier->getSlug(),
                    'sent' => true,
                ]));

            }
        }

        $previous = $repoCourrier->findPrevious($courrier, $isAdmin);
        $next = $repoCourrier->findNext($courrier, $isAdmin);


        return $this->render('courrier/voir.html.twig', [
            'courrier' => $courrier,
            'prev_courrier' => $previous,
            'next_courrier' => $next,
            'reaction_form' => $formReaction->createView(),
            'sent' => $sent,
        ]);
    }


    /**
     * @Route("/blog/courriers/rechercher/", name="courrier_rechercher")
     *
     */
    public function rechercherAction(Request $request)
    {
        $query = $request->query->get('s');

        if (!is_null($query)) {
            $courriers = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Courrier')
                ->findLikeBy([
                    'name' => $query,
                ])
            ;

            return $this->render('blog/rechercher.html.twig', [
                'courriers' => $courriers,
                'query_string' => $query,
            ]);
        }

        return new Response();
    }

    /**
     * @Route("/blog/courriers/push/", name="courrier_apercu")
     */
    function apercuAction(Request $request, $id = null)
    {
        if (is_null($id)) {
            $id = explode('-', $request->request->get('id'))[1];
        }

        $noMoreCourrier = false;

        $courrier = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
            ->find($id)
        ;

        $requestStack = $this->get('request_stack');
        if (null === $requestStack->getParentRequest()) {
            $courrier = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Courrier')
                ->findLast(1, $courrier->getEnvoi(), $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
            ;
            $courrier = array_shift($courrier);

            if (empty($courrier)) {
                $noMoreCourrier = true;
            }
        }

        return $this->render('courrier/apercu.html.twig', [
            'courrier' => $courrier,
            'no_more_courrier' => $noMoreCourrier,
        ]);
    }

    /**
     * @Route("/blog/courriers/popup/", name="courrier_popup")
     *
     * @param Request $request
     * @return Reponse|JsonResponse
     */
    public function popupAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {
            $slugCourrier = $request->request->get('courrier');

            $courrier = $this
                ->getDoctrine()
                ->getRepository('AppBundle:Courrier')
                ->findOneBySlug($slugCourrier)
            ;

            if (empty($courrier)) {
                throw new NotFoundHttpException();
            }

            $courrier = [
                'name' => $courrier->getName(),
                'date' => $courrier->getEnvoi()->format('d/m/Y'),
                'image' => $courrier->getImage()->getPath(),
                'intro' => $courrier->getIntro(),
            ];

            return new JsonResponse($courrier);
        }

        return new Response(null, 405);
    }

    /**
     * @Route("blog/courrier/vote", name="courrier_vote")
     */
    public function voteAction(Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod(Request::METHOD_POST)) {

            $post = $request->request;
            $idReaction = $post->get('id');
            $action = ucfirst($post->get('action'));
            $getter = sprintf('get%s', $action);
            $setter = sprintf('set%s', $action);

            $reactionAlreadyVoted = json_decode($request->cookies->get('reaction_votes'), true);

            if (in_array($idReaction, $reactionAlreadyVoted)) {
//                throw new AccessDeniedHttpException('Vous avez déjà voté pour ce commentaire');
            }

            $em = $this
                ->getDoctrine()
                ->getManager()
            ;

            $reaction = $em
                ->getRepository('AppBundle:Reaction')
                ->find($idReaction)
            ;

            $voteCount = $reaction->$getter() + 1;
            $reaction->$setter($voteCount);

            $em->persist($reaction);
            $em->flush();

            $response = new Response();
            $cookie = new Cookie('reaction_votes', json_encode([$reaction->getId()]), new \DateTime('+1 month'), '/');
            $response->headers->setCookie($cookie);
            $response->send();

            return new JsonResponse([
                'vote' => $voteCount,
                'totalVotes' => $reaction->getTotalVotes(),
            ]);
        }

        return new Response(null, 405);
    }

    /**
     * Not routable
     */
    function sliderAction($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Courrier')
        ;

        /** @var Courrier $courrier */
        $courrier = $repository->find($id);

        $tags = [];
        foreach (iterator_to_array($courrier->getTags()) as $tag) {
            $tags[] = $tag->getId();
        }


        $courriers = [];
        foreach ($tags as $idTag) {
            foreach ($repository->findByTag($idTag) as $fetchedCourrier) {
                $courriers[$fetchedCourrier->getId()] = $fetchedCourrier;
            }
        }

        unset($courriers[$courrier->getId()]);

        if (empty($courriers)) {
            return new Response();
        }

        return $this->render('courrier/slider.html.twig', [
            'courriers' => $courriers,
        ]);
    }
}
