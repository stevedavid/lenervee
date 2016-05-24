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

class TacheController extends Controller
{
    /**
     * @Route("/admin/taches/images-cleaner", name="admin_tache_image")
     *
     * @return Response
     */
    public function imageAction(Request $request)
    {
        ini_set('max_execution_time', -1);
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'nrv:clean:images',
        ));
        // You can use NullOutput() if you don't need the output
        $output = new StreamedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);
    }
}