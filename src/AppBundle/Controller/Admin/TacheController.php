<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Console\StreamedOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TacheController extends Controller
{
    const COMMANDS = [
        'facebook-fetcher' => 'nrv:fetch:facebook',
        'images-cleaner' => 'nrv:clean:images',
    ];

    /**
     * @Route("admin/taches/{command}", requirements={"command" = "facebook-fetcher|images-cleaner"}, name="admin_tache_index")
     */
    public function indexAction($command)
    {
        if (!in_array($command, array_keys(self::COMMANDS))) {
            throw new NotFoundHttpException('Page non trouvée');
        }

        return $this->render('admin/tache/index.html.twig', [
            'command' => $command,
        ]);
    }

    /**
     * @Route("/admin/taches/_do", name="admin_tache_do")
     *
     * @return Response
     */
    public function doAction(Request $request)
    {
        $command = self::COMMANDS[$request->request->get('command')];

        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => $command,
        ));
        // You can use NullOutput() if you don't need the output
        $output = new StreamedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
//        return new Response($content);
        exit;
    }
}