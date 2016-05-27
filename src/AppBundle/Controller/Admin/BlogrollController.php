<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

class BlogrollController extends Controller
{
    /**
     * @Route("admin/blogroll", name="admin_blogroll_lister")
     */
    public function listerAction()
    {
        $yamlPath = $this->get('kernel')->getRootDir() . '/../' . $this->getParameter('blogroll_yml_path');

        return $this->render('admin/blogroll/lister.html.twig', [
            'liens' => (new Parser)->parse(file_get_contents($yamlPath))['blogroll'],
        ]);
    }

    /**
     * @Route("admin/blogroll/sauvegarder", name="admin_blogroll_sauvegarder")
     */
    public function sauvegarderAction(Request $request)
    {
        $yamlPath = $this->get('kernel')->getRootDir() . '/../' . $this->getParameter('blogroll_yml_path');
        $dumper = new Dumper();

        $yaml = $dumper->dump(['blogroll' => $request->request->get('yaml')], 2);

        file_put_contents($yamlPath, $yaml);

        return new Response();
    }
}